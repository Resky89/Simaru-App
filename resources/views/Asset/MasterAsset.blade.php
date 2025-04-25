@extends('Layout.app')

@section('title', 'Master Asset Management')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Asset Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">MASTER ASSET</h1>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button id="importMasterAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                            </svg>
                            <span class="text-base">Import Excel</span>
                        </button>
                        <button id="addMasterAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Add Master Asset</span>
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Search by asset name, type, or brand..."
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
                            <option value="">All Types</option>
                            @foreach($assetTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>

                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
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
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Type</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Subcategory</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Brand</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Depreciable</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Calibration</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($masterAssets) && count($masterAssets) > 0)
                                @foreach($masterAssets as $asset)
                                <tr data-asset-id="{{ $asset['asset_master_id'] ?? '' }}">
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_master_id'] ?? '' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if(isset($asset['asset_type']))
                                            @if(strtolower($asset['asset_type']) == 'medical')
                                                Medical
                                            @elseif(strtolower($asset['asset_type']) == 'non_medical')
                                                Non Medical
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
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Yes</span>
                                        @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">No</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        @if(isset($asset['needs_calibration']) && $asset['needs_calibration'])
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Yes</span>
                                        @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">No</span>
                                        @endif
                                    </td>
                                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-asset-btn"
                                                data-id="{{ $asset['asset_master_id'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-asset-btn"
                                                data-id="{{ $asset['asset_master_id'] ?? '' }}"
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
                                    <td colspan="8" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No master assets found</td>
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
                            Prev
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
                            Next
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
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($masterAssets ?? []) }} of {{ count($masterAssets ?? []) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Master Asset Modal -->
<div id="addMasterAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="addMasterAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD MASTER ASSET</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('asset-master.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Image upload - Improved visibility -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#213268]">Asset Image <span class="text-sm font-normal text-[#666666]">(Upload a reference image for this asset)</span></label>
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
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: jpg, jpeg, png</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
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

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Asset Type</label>
                                    <select name="asset_type" id="asset_type" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select Asset Type</option>
                                        <option value="medical">medical</option>
                                        <option value="non_medical">non_medical</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Subcategory</label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Search subcategory...">
                                        <input type="hidden" name="subcategory_id" id="subcategory_id" required>
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500">Type to search...</div>
                                            @foreach($subcategories as $subcategory)
                                            <div class="option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]"
                                                data-value="{{ $subcategory['subcategory_id'] }}"
                                                data-type="{{ $subcategory['asset_type'] }}">
                                                {{ $subcategory['subcategory_name'] }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Brand</label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Search brand...">
                                        <input type="hidden" name="brand_id" required>
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500">Type to search...</div>
                                            @foreach($brands as $brand)
                                            <div class="option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]"
                                                data-value="{{ $brand['brand_id'] }}">
                                                {{ $brand['brand_name'] }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea name="description"
                                    class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                    placeholder="Asset description"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Depreciation Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="is_depreciable" class="text-base font-semibold text-[#666666]">Enable Asset Depreciation</label>
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
                                    <label for="needs_calibration" class="text-base font-semibold text-[#666666]">Needs Calibration</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="needs_calibration" id="needs_calibration" class="sr-only peer calibration-toggle" value="true">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">No</span>
                                    </label>
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

<!-- Edit Master Asset Modal -->
<div id="editMasterAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editMasterAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT MASTER ASSET</h2>
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
                            <p class="mt-2 text-gray-600">Loading asset data...</p>
                        </div>

                        <div id="edit-form-content" class="space-y-4 hidden">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Image upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Asset Image</label>
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
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-blue-600">browse</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                    </div>
                                    <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
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

                                <!-- Asset Type Field -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Asset Type</label>
                                    <select name="asset_type" id="edit_asset_type" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select Asset Type</option>
                                        <option value="non_medical">Non Medical</option>
                                        <option value="medical">Medical</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Subcategory</label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Search subcategory...">
                                        <input type="hidden" name="subcategory_id" id="edit_subcategory_id" required>
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500">Type to search...</div>
                                            @foreach($subcategories as $subcategory)
                                            <div class="option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]"
                                                data-value="{{ $subcategory['subcategory_id'] }}">
                                                {{ $subcategory['subcategory_name'] }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Dropdown - Moved from below into this grid -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Brand</label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Search brand...">
                                        <input type="hidden" name="brand_id" id="edit_brand_id" required>
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500">Type to search...</div>
                                            @foreach($brands as $brand)
                                            <div class="option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]"
                                                data-value="{{ $brand['brand_id'] }}">
                                                {{ $brand['brand_name'] }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea name="description" id="edit_description"
                                    class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                    placeholder="Asset description"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Depreciation Toggle Switch -->
                                <div class="flex items-center justify-between">
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

                                <!-- Calibration Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="edit_needs_calibration" class="text-base font-semibold text-[#666666]">Needs Calibration</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="needs_calibration" id="edit_needs_calibration" class="sr-only peer calibration-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="edit-submit-btn" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE MASTER ASSET</h2>
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
                                <p class="text-base text-gray-600 text-center">Are you sure you want to delete this master asset? This action cannot be undone.</p>
                                <p id="delete-asset-name" class="text-base font-semibold text-center mt-2"></p>
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

<!-- Import Master Asset Modal -->
<div id="importMasterAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="importMasterAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPORT MASTER ASSETS</h2>
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
                                    <li>Required columns: Asset Name, Asset Type, Subcategory, Brand</li>
                                    <li>Maximum 100 records per import</li>
                                    <li>File types supported: .xlsx, .xls, .csv</li>
                                </ul>
                                <div class="mt-3 flex justify-end">
                                    <a href="{{ asset('docs/ImportAsetTemplate.xlsx') }}" download class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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

                <!-- Step 2: Data Preview -->
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
                                            <th class="p-3 text-left text-xs font-semibold">Asset Name</th>
                                            <th class="p-3 text-left text-xs font-semibold">Asset Type</th>
                                            <th class="p-3 text-left text-xs font-semibold">Subcategory</th>
                                            <th class="p-3 text-left text-xs font-semibold">Brand</th>
                                            <th class="p-3 text-center text-xs font-semibold">Depreciable</th>
                                            <th class="p-3 text-center text-xs font-semibold">Calibration</th>
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
                                <form action="{{ route('asset-master.import') }}" method="POST" id="import-form" class="w-2/3" enctype="multipart/form-data">
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

@if(session('error') || isset($error))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p>{!! session('error') ?? $error ?? 'An error occurred' !!}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>
@endif

@endsection

@push('scripts')
<!-- SheetJS library for Excel parsing -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                    loadMoreDiv.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
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
                            this.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
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
                                loadMoreDiv.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
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
                                        this.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
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
                                countDiv.textContent = `Found ${matchingOptions.length} matches`;
                                optionsContainer.insertBefore(countDiv, optionsContainer.firstChild);
                            }
                        }
                    }
                }

                // Search functionality with debounce
                searchInput.addEventListener('input', debounce(function() {
                    const searchValue = this.value.toLowerCase().trim();

                    if (searchValue === '') {
                        // If search is cleared, reset to show initial options
                        if (isFullyLoaded) {
                            allOptions.forEach(option => {
                                option.style.display = '';
                            });
                        } else {
                            // Only show limited options
                            allOptions.forEach((option, index) => {
                                option.style.display = index < visibleCount ? '' : 'none';
                            });

                            // Make sure "load more" is visible
                            if (!optionsContainer.querySelector('.load-more')) {
                                const loadMoreDiv = document.createElement('div');
                                loadMoreDiv.className = 'load-more p-3 text-center text-blue-600 hover:bg-gray-100 cursor-pointer';
                                loadMoreDiv.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
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
                                        this.textContent = `Load more options... (${visibleCount} of ${allOptions.length})`;
                                    }
                                });
                                optionsContainer.appendChild(loadMoreDiv);
                            }
                        }

                        // Remove any no-results message
                        const existingNoResults = optionsContainer.querySelector('.no-results');
                        if (existingNoResults) {
                            existingNoResults.remove();
                        }

                        // Remove any results count message
                        const resultsCount = optionsContainer.querySelector('.results-count');
                        if (resultsCount) {
                            resultsCount.remove();
                        }
                    } else {
                        // For search: always search through all options (even if they were hidden by lazy loading)
                        // This ensures we find all matches even in lazy loaded data
                        filterOptions(searchValue);
                    }
                }, 200)); // Reduced debounce time for more responsiveness

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
        function setSelectValue(selectId, value) {
            if (!value) return;

            // Get the elements
            const hiddenInput = document.getElementById(selectId);
            if (!hiddenInput) return;

            const container = hiddenInput.closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const options = container.querySelectorAll('.option');

            // Set the hidden input value
            hiddenInput.value = value.toString();

            // Find the matching option to display its text
            let foundOption = null;

            // Try to find the option in the DOM
            Array.from(options).forEach(option => {
                if (option.dataset.value === value.toString()) {
                    foundOption = option;
                }
            });

            // If option not found in DOM, try to find it in the data
            if (!foundOption) {
                if (selectId === 'edit_subcategory_id') {
                    const subcategories = @json($subcategories);
                    const subcategory = subcategories.find(sc => sc.subcategory_id.toString() === value.toString());
                    if (subcategory) {
                        console.log('Found subcategory in data:', subcategory);
                        searchInput.value = subcategory.subcategory_name || value.toString();

                        // Ensure this option is visible
                        ensureOptionVisible(container, value);
                        return;
                    }
                } else if (selectId === 'edit_brand_id') {
                    const brands = @json($brands);
                    const brand = brands.find(b => b.brand_id.toString() === value.toString());
                    if (brand) {
                        console.log('Found brand in data:', brand);
                        searchInput.value = brand.brand_name || value.toString();

                        // Ensure this option is visible
                        ensureOptionVisible(container, value);
                        return;
                    }
                }
            }

            // Update the display text
            if (foundOption) {
                searchInput.value = foundOption.textContent.trim();

                // Make sure this option is visible
                foundOption.style.display = '';

                // Ensure we can see this option
                setTimeout(() => {
                    foundOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            } else {
                console.warn(`Option with value "${value}" not found for ${selectId}`);
                searchInput.value = value.toString();
            }
        }

        // Initialize all custom selects
        initCustomSelects();

        // Update asset type filter for subcategory
        document.getElementById('asset_type')?.addEventListener('change', function() {
            const selectedType = this.value;
            const container = document.querySelector('#subcategory_id').closest('.custom-select-container');
            const options = container.querySelectorAll('.option');

            options.forEach(option => {
                const optionType = option.dataset.type;
                if (!selectedType || option.value === '' || optionType === selectedType) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Reset the subcategory selection
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const searchInput = container.querySelector('.search-input');
            hiddenInput.value = '';
            searchInput.value = '';
        });

        // Add master asset button
        document.getElementById('addMasterAssetBtn')?.addEventListener('click', function() {
            const modal = document.getElementById('addMasterAssetModal');
            const content = document.getElementById('addMasterAssetModalContent');
            if (modal && content) {
                openModal(modal, content);
            }
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
                        if (!data || !data.masterAsset) {
                            throw new Error('Invalid response data structure');
                        }

                        const asset = data.masterAsset;
                        console.log('Fetched asset data for image check:', asset);
                        console.log('Image URL property value:', asset.reference_image_path);
                        console.log('All asset properties:', Object.keys(asset));

                        // Fill in form fields
                        document.getElementById('edit_asset_name').value = asset.asset_name || '';
                        document.getElementById('edit_description').value = asset.description || '';

                        // Set asset type
                        if (asset.asset_type) {
                            document.getElementById('edit_asset_type').value = asset.asset_type;
                        }

                        // Set subcategory and brand using the helper function
                        setSelectValue('edit_subcategory_id', asset.subcategory_id);
                        setSelectValue('edit_brand_id', asset.brand_id);

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

                                    console.log('Using master asset image URL:', imageUrl);

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
                                        console.log(`Successfully loaded image from URL: ${imageUrl}`);
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
                                    <p class="font-medium">Error fetching asset data</p>
                                    <p>${error.message}</p>
                                    <p class="mt-2">Please try again or contact support if the problem persists.</p>
                                </div>
                                <button type="button" class="close-modal w-full h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300">
                                    Close
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

        // Modal close buttons
        document.querySelectorAll('.close-modal').forEach(closeButton => {
            closeButton.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="Content"]');

                // If it's the edit master asset modal, reset the form
                if (modal.id === 'editMasterAssetModal') {
                    resetEditMasterAssetForm();
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

                    // If it's the edit master asset modal, reset the form
                    if (this.id === 'editMasterAssetModal') {
                        resetEditMasterAssetForm();
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

        // Function to find subcategory name by ID
        function getSubcategoryNameById(id) {
            const subcategories = @json($subcategories);
            const subcategory = subcategories.find(sc => sc.subcategory_id.toString() === id.toString());
            return subcategory ? subcategory.subcategory_name : 'Unknown Subcategory';
        }

        // Function to ensure specific options are always shown if they match
        function ensureOptionVisible(container, value) {
            if (!value) return;

            const optionsContainer = container.querySelector('.options-container');
            const allOptions = Array.from(container.querySelectorAll('.option'));
            const targetOption = allOptions.find(option => option.dataset.value === value.toString());

            if (targetOption) {
                targetOption.style.display = '';

                // If we have an exact match, scroll to it
                setTimeout(() => {
                    targetOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
        }

        // Make sure the selected options are always visible regardless of lazy loading
        document.querySelectorAll('.custom-select-container').forEach(container => {
            const hiddenInput = container.querySelector('input[type="hidden"]');
            if (hiddenInput && hiddenInput.value) {
                ensureOptionVisible(container, hiddenInput.value);
            }
        });

        // Add event listeners for asset type selection to filter subcategories
        function initAssetTypeFilters() {
            // For add modal
            document.getElementById('asset_type')?.addEventListener('change', function() {
                filterSubcategoriesByAssetType(this.value, 'subcategory_id');
            });

            // For edit modal
            document.getElementById('edit_asset_type')?.addEventListener('change', function() {
                filterSubcategoriesByAssetType(this.value, 'edit_subcategory_id');
            });
        }

        // Function to filter subcategories by selected asset type
        function filterSubcategoriesByAssetType(assetType, subcategoryFieldId) {
            if (!assetType) return;

            console.log(`Filtering subcategories for asset type: ${assetType}`);

            const subcategoryContainer = document.getElementById(subcategoryFieldId).closest('.custom-select-container');
            const searchInput = subcategoryContainer.querySelector('.search-input');
            const hiddenInput = subcategoryContainer.querySelector('input[type="hidden"]');
            const optionsContainer = subcategoryContainer.querySelector('.options-container');
            const options = Array.from(subcategoryContainer.querySelectorAll('.option'));

            // Clear current selection since we're changing the available options
            searchInput.value = '';
            hiddenInput.value = '';

            // Remove any existing messages
            const existingMessages = optionsContainer.querySelectorAll('.no-results, .results-count, .load-more');
            existingMessages.forEach(el => el.remove());

            // Count matching options for this asset type
            let matchingCount = 0;
            let visibleCount = 0;
            const maxInitialOptions = 30;

            // Filter options based on asset type attribute
            options.forEach((option, index) => {
                const optionAssetType = option.getAttribute('data-type');

                if (optionAssetType === assetType) {
                    matchingCount++;

                    // Show only the first batch
                    if (matchingCount <= maxInitialOptions) {
                        option.style.display = '';
                        visibleCount++;
                    } else {
                        option.style.display = 'none';
                    }
                } else {
                    option.style.display = 'none';
                }
            });

            // Add load more button if there are more matching options
            if (matchingCount > maxInitialOptions) {
                const loadMoreDiv = document.createElement('div');
                loadMoreDiv.className = 'load-more p-3 text-center text-blue-600 hover:bg-gray-100 cursor-pointer';
                loadMoreDiv.textContent = `Load more options... (${visibleCount} of ${matchingCount})`;

                loadMoreDiv.addEventListener('click', function() {
                    // Load more filtered options
                    let newVisible = 0;
                    let loaded = 0;
                    const loadMoreIncrement = 50;

                    options.forEach(option => {
                        const optionAssetType = option.getAttribute('data-type');

                        if (optionAssetType === assetType) {
                            newVisible++;

                            if (newVisible > visibleCount && loaded < loadMoreIncrement) {
                                option.style.display = '';
                                loaded++;
                            }
                        }
                    });

                    visibleCount += loaded;

                    // Update or remove load more button
                    if (visibleCount >= matchingCount) {
                        this.remove();
                    } else {
                        this.textContent = `Load more options... (${visibleCount} of ${matchingCount})`;
                    }
                });

                optionsContainer.appendChild(loadMoreDiv);
            } else if (matchingCount === 0) {
                // No matching subcategories
                const msgDiv = document.createElement('div');
                msgDiv.className = 'no-results p-3 text-center text-gray-500';
                msgDiv.textContent = `No subcategories found for ${assetType === 'medical' ? 'Medical' : 'Non Medical'} asset type`;
                optionsContainer.appendChild(msgDiv);
            }

            // Add count message
            const countDiv = document.createElement('div');
            countDiv.className = 'results-count p-2 text-center text-xs text-gray-500';
            countDiv.textContent = `${matchingCount} subcategories for ${assetType === 'medical' ? 'Medical' : 'Non Medical'}`;
            optionsContainer.insertBefore(countDiv, optionsContainer.firstChild);

            console.log(`Found ${matchingCount} subcategories for asset type ${assetType}`);
        }

        // Initialize custom selects and asset type filters
        initCustomSelects();
        initAssetTypeFilters();

        // If asset type is already selected on page load, filter subcategories
        const addAssetType = document.getElementById('asset_type');
        if (addAssetType && addAssetType.value) {
            filterSubcategoriesByAssetType(addAssetType.value, 'subcategory_id');
        }

        const editAssetType = document.getElementById('edit_asset_type');
        if (editAssetType && editAssetType.value) {
            filterSubcategoriesByAssetType(editAssetType.value, 'edit_subcategory_id');
        }

        // Function to reset the edit master asset form
        function resetEditMasterAssetForm() {
            // Reset file input
            const fileInput = document.getElementById('edit_image_file');
            if (fileInput) {
                fileInput.value = '';
            }

            // Hide image preview
            const imageContainer = document.getElementById('edit-image-container');
            if (imageContainer) {
                imageContainer.classList.add('hidden');
            }

            // Remove any remove_image flag
            const form = document.getElementById('editMasterAssetForm');
            if (form) {
                const removeImageInput = form.querySelector('input[name="remove_image"]');
                if (removeImageInput) {
                    removeImageInput.remove();
                }
            }
        }

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

        // Function to apply filters and sorting
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value;
            const assetTypeFilter = document.getElementById('assetTypeFilter').value;
            const sortOrder = document.getElementById('sortOrder').value;

            console.log('Applying filters:', {
                search: searchTerm,
                assetType: assetTypeFilter,
                sort: sortOrder
            });

            const url = new URL(window.location.href);

            // Set search parameter
            if (searchTerm) url.searchParams.set('search', searchTerm);
            else url.searchParams.delete('search');

            // Set asset type parameter
            if (assetTypeFilter) url.searchParams.set('type', assetTypeFilter);
            else url.searchParams.delete('type');

            // Set sort parameter
            if (sortOrder) url.searchParams.set('sort', sortOrder);
            else url.searchParams.delete('sort');

            // Reset to first page on filter change
            url.searchParams.set('page', 1);

            console.log('Filter URL:', url.toString());

            // Redirect to new URL with filters
            window.location.href = url.toString();
        }

        // Apply debounce to search input
        document.getElementById('searchInput')?.addEventListener('input', debounce(function() {
            applyFilters();
        }, 500));

        // Asset type filter - apply immediately on change
        document.getElementById('assetTypeFilter')?.addEventListener('change', function() {
            console.log('Asset Type Changed:', this.value);
            applyFilters();
        });

        // Sort order - apply immediately on change
        document.getElementById('sortOrder')?.addEventListener('change', function() {
            applyFilters();
        });

        // Set existing values from URL
        function setFilterValuesFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);

            // Set search input value
            if (urlParams.has('search')) {
                document.getElementById('searchInput').value = urlParams.get('search');
            }

            // Set asset type filter value
            if (urlParams.has('type')) {
                const assetType = urlParams.get('type');
                console.log('Setting asset type from URL:', assetType);
                const assetTypeFilter = document.getElementById('assetTypeFilter');
                if (assetTypeFilter) {
                    // First check if the value exists in the options
                    let found = false;
                    for (let i = 0; i < assetTypeFilter.options.length; i++) {
                        if (assetTypeFilter.options[i].value === assetType) {
                            assetTypeFilter.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }

                    // If not found, add it and select
                    if (!found && assetType) {
                        const option = new Option(assetType, assetType);
                        assetTypeFilter.add(option);
                        assetTypeFilter.value = assetType;
                    }

                    console.log('Asset type filter value after set:', assetTypeFilter.value);
                }
            }

            // Set sort order value
            if (urlParams.has('sort')) {
                document.getElementById('sortOrder').value = urlParams.get('sort');
            }
        }

        // Initialize filter values from URL on page load
        document.addEventListener('DOMContentLoaded', function() {
            setFilterValuesFromUrl();
        });

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
                excelError.textContent = 'Please select an Excel file first.';
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
                    excelError.textContent = 'The Excel file appears to be empty or could not be parsed.';
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
                console.error('Error parsing Excel file:', error);
                excelLoading.classList.add('hidden');
                previewBtn.disabled = false;
                excelError.textContent = 'Error parsing Excel file: ' + error.message;
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
                    warnings.push(`Row ${i+1}: Missing asset name`);
                }

                if (!rowData.asset_type) {
                    warnings.push(`Row ${i+1}: Missing asset type`);
                } else {
                    // Normalize asset_type
                    if (rowData.asset_type.toLowerCase().includes('medical')) {
                        rowData.asset_type = 'medical';
                    } else {
                        rowData.asset_type = 'non_medical';
                    }
                }

                if (!rowData.subcategory_name) {
                    warnings.push(`Row ${i+1}: Missing subcategory`);
                }

                if (!rowData.brand_name) {
                    warnings.push(`Row ${i+1}: Missing brand`);
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
            previewCount.textContent = `${data.length} items found`;

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
                            '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Yes</span>' :
                            '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">No</span>'
                        }
                    </td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        ${item.needs_calibration ?
                            '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Yes</span>' :
                            '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">No</span>'
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
                        warnings.push(`Duplicate entry found: "${item.asset_name}" (${formatAssetType(item.asset_type)}, ${item.subcategory_name}, ${item.brand_name})`);
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
                return 'Medical';
            } else if (lowerType === 'non_medical') {
                return 'Non Medical';
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
                    <span>Importing...</span>
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
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right';
            notification.role = 'alert';

            if (type === 'success') {
                notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Success!</p>
                            <p>${message}</p>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            } else {
                notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Error!</p>
                            <p>${message}</p>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            }

            // Add to document
            document.body.appendChild(notification);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }

        // Add slide-in animation to CSS
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
