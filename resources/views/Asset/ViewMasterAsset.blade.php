@extends('Layout.app')

@section('title', 'Master Asset Details')

@section('content')
<div class="p-4 md:p-6">
    <!-- Header with title and back button -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-[32px] font-semibold text-[#28356B]">
            MASTER ASSET: {{ $masterAsset['asset_name'] ?? 'Asset Details' }}
        </h1>
        <div class="flex gap-2">
            <button data-master-asset-id="{{ $masterAsset['asset_master_id'] ?? '' }}" class="edit-master-asset-btn flex items-center gap-2 px-4 py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit
            </button>
            <a href="{{ route('export-master-asset-pdf', ['id' => $masterAsset['asset_master_id'] ?? '']) }}" target="_blank" class="flex items-center gap-2 px-4 py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </a>
            <a href="{{ route('asset-master') }}" class="flex items-center gap-2 px-4 py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Master Asset Information -->
    <div class="mb-6">
        <div class="bg-[#28356B] rounded-t-lg p-4">
            <h2 class="text-white font-semibold">Master Asset Information</h2>
        </div>
        <div class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Asset Image -->
                <div>
                    @if(isset($masterAsset['reference_image_path']) && !empty($masterAsset['reference_image_path']))
                        <img src="{{ config('app.backend_url') }}/public{{ $masterAsset['reference_image_path'] }}"
                            alt="{{ $masterAsset['asset_name'] }}"
                            class="w-full h-auto object-cover rounded-md bg-gray-50 p-2"
                            onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4');">
                    @else
                        <div class="w-full h-48 bg-gray-100 rounded-md flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif
                </div>

                <!-- Asset Details - First Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Asset Code</p>
                        <p class="font-medium">{{ $masterAsset['asset_master_code'] ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Asset Type</p>
                        <p class="font-medium">{{ isset($masterAsset['asset_type']) ? ucwords(str_replace('_', ' ', $masterAsset['asset_type'])) : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Subcategory</p>
                        <p class="font-medium">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Asset Details - Second Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Brand</p>
                        <p class="font-medium">{{ $masterAsset['brand_name'] ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($masterAsset['created_at'] ?? now())->format('d M Y H:i') }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Characteristics</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if(isset($masterAsset['is_depreciable']) && $masterAsset['is_depreciable'])
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Depreciable</span>
                            @endif

                            @if(isset($masterAsset['needs_calibration']) && $masterAsset['needs_calibration'])
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Needs Calibration</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description (Full Width) -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">Description</p>
                <p class="mt-1">{{ $masterAsset['description'] ?? 'No description available' }}</p>
            </div>
        </div>
    </div>

    <!-- Asset Units Section -->
    <div>
        <div class="bg-[#28356B] rounded-t-lg p-4 flex justify-between items-center">
            <h2 class="text-white font-semibold">Asset Units</h2>
            <div class="bg-white text-[#28356B] text-sm font-bold px-3 py-1 rounded-full">
                Total: {{ count($masterAsset['linked_assets'] ?? []) }} units
            </div>
        </div>

        @if(isset($masterAsset['linked_assets']) && count($masterAsset['linked_assets']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Asset Code</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Serial Number</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Condition</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Status</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Location</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterAsset['linked_assets'] as $asset)
                            <tr>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    {{ $asset['asset_code'] }}
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    {{ $asset['serial_number'] }}
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    @php
                                        $conditionClass = 'bg-gray-100 text-gray-800';

                                        if(isset($asset['condition'])) {
                                            switch(strtolower($asset['condition'])) {
                                                case 'good':
                                                    $conditionClass = 'bg-green-100 text-green-800';
                                                    break;
                                                case 'fair':
                                                    $conditionClass = 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'poor':
                                                    $conditionClass = 'bg-orange-100 text-orange-800';
                                                    break;
                                                case 'high damage':
                                                    $conditionClass = 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conditionClass }}">
                                        {{ ucfirst($asset['condition'] ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';

                                        if(isset($asset['current_status'])) {
                                            switch(strtolower($asset['current_status'])) {
                                                case 'available':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    break;
                                                case 'in_use':
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'maintenance':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'broken':
                                                    $statusClass = 'bg-orange-100 text-orange-800';
                                                    break;
                                                case 'dispose':
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $asset['current_status'] ?? 'Unknown')) }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    <div>{{ $asset['room_name'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $asset['building_name'] ?? 'N/A' }}</div>
                                </td>
                                <td class="p-3 border-t border-gray-200 text-center">
                                    <a href="{{ route('asset.details', $asset['asset_id']) }}" class="text-[#28356B] hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white p-4 text-center border border-t-0 border-gray-200 rounded-b-lg">
                <p class="text-gray-500">No assets linked to this master asset.</p>
            </div>
        @endif
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
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#28356B]">EDIT MASTER ASSET</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="editMasterAssetForm" method="POST" action="{{ route('asset-master.update', $masterAsset['asset_master_id']) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <!-- Loading indicator -->
                            <div class="text-center" id="editFormSpinner">
                                <div class="inline-block w-8 h-8 border-4 border-[#28356B] border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-gray-600">Loading asset data...</p>
                            </div>

                            <div id="edit-form-content" class="space-y-4 hidden">
                                <!-- Asset Information Section -->
                                <h3 class="text-lg font-semibold text-[#28356B] border-b pb-2">Asset Information</h3>

                                <!-- Image upload -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Asset Image</label>
                                    <div class="border-2 border-dashed border-[#28356B] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                        <!-- Current image preview -->
                                        <div id="edit_image_preview" class="mt-2 mb-4 w-full hidden">
                                            <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                <img id="edit_current_image" src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Asset Image">
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
                                            <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-[#28356B] font-semibold">browse files</span></p>
                                            <p class="mt-1 text-xs text-gray-500">Accepted formats: jpg, jpeg, png</p>
                                        </div>
                                        <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>

                                <!-- Basic Asset Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="edit_asset_name" class="block text-base font-semibold text-[#666666]">Asset Name</label>
                                        <input type="text" name="asset_name" id="edit_asset_name" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#28356B]"
                                            placeholder="Asset name">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="edit_asset_type" class="block text-base font-semibold text-[#666666]">Asset Type</label>
                                        <select name="asset_type" id="edit_asset_type" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#28356B]">
                                            <option value="">Select Asset Type</option>
                                            <option value="medical">Medical</option>
                                            <option value="non_medical">Non Medical</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="edit_subcategory_id" class="block text-base font-semibold text-[#666666]">Subcategory</label>
                                        <div class="custom-select-container relative">
                                            <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#28356B]"
                                                placeholder="Search subcategory...">
                                            <input type="hidden" name="subcategory_id" id="edit_subcategory_id" required>
                                            <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 text-center text-gray-500">Type to search...</div>
                                                @foreach($subcategories as $subcategory)
                                                <div class="option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]"
                                                    data-value="{{ $subcategory['subcategory_id'] }}"
                                                    data-type="{{ $subcategory['asset_type'] ?? '' }}">
                                                    {{ $subcategory['subcategory_name'] }}
                                                </div>
                                            @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="edit_brand_id" class="block text-base font-semibold text-[#666666]">Brand</label>
                                        <div class="custom-select-container relative">
                                            <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#28356B]"
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
                                    <label for="edit_description" class="block text-base font-semibold text-[#666666]">Description</label>
                                    <textarea name="description" id="edit_description"
                                        class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#28356B] resize-none"
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
                                                peer-checked:bg-[#28356B]"></div>
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
                                                peer-checked:bg-[#28356B]"></div>
                                            <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" id="edit-submit-btn" class="w-full h-[45px] bg-[#28356B] text-white rounded-lg text-base hover:bg-[#1e2c5a] transform active:scale-[0.98] transition-all duration-200 mt-6">
                                    Update
                                    </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Custom select dropdown functionality
        function initCustomSelects() {
            document.querySelectorAll('.custom-select-container').forEach(container => {
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const optionsContainer = container.querySelector('.options-container');
                const options = container.querySelectorAll('.option');

                // Show options when input is focused
                searchInput.addEventListener('focus', () => {
                    optionsContainer.classList.remove('hidden');
                });

                // Hide options when clicking outside
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) {
                        optionsContainer.classList.add('hidden');
                    }
                });

                // Search functionality
                searchInput.addEventListener('input', function() {
                    const searchValue = this.value.toLowerCase().trim();
                    let hasResults = false;

                    // Remove any existing no-results message
                    const existingNoResults = optionsContainer.querySelector('.no-results');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    options.forEach(option => {
                        const text = option.textContent.trim().toLowerCase();
                        if (text.includes(searchValue)) {
                            option.style.display = '';
                            hasResults = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    // Show no results message if needed
                    if (!hasResults) {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'no-results p-3 text-center text-gray-500';
                        msgDiv.textContent = 'No results found';
                        optionsContainer.appendChild(msgDiv);
                    }
                });

                // Set selected option
                options.forEach(option => {
                    option.addEventListener('click', () => {
                        const value = option.dataset.value;
                        const text = option.textContent.trim();

                        hiddenInput.value = value;
                        searchInput.value = text;
                        optionsContainer.classList.add('hidden');

                        // Set asset type from subcategory if available
                        if (hiddenInput.id === 'edit_subcategory_id' && option.dataset.type) {
                            const assetTypeSelect = document.getElementById('edit_asset_type');
                            if (assetTypeSelect) {
                                assetTypeSelect.value = option.dataset.type;
                            }
                        }
                    });
                });
            });
        }

        // Function to set select value and display text for custom selects
        function setSelectValue(selectId, value) {
            if (!value) return;

            // Get the elements
            const container = document.querySelector(`input#${selectId}`).closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = document.getElementById(selectId);
            const options = container.querySelectorAll('.option');

            // Set the hidden input value
            hiddenInput.value = value.toString();

            // Find the matching option to display its text
            let foundOption = null;
            options.forEach(option => {
                if (option.dataset.value === value.toString()) {
                    foundOption = option;
                }
            });

            // If option found, set text and ensure it's visible
            if (foundOption) {
                searchInput.value = foundOption.textContent.trim();
            } else {
                // Try to find name from data if option not found in DOM
                if (selectId === 'edit_subcategory_id') {
                    const subcategories = @json($subcategories);
                    const subcategory = subcategories.find(sc => sc.subcategory_id.toString() === value.toString());
                    if (subcategory) {
                        searchInput.value = subcategory.subcategory_name || value.toString();
                    } else {
                        searchInput.value = value.toString();
                    }
                } else if (selectId === 'edit_brand_id') {
                    const brands = @json($brands);
                    const brand = brands.find(b => b.brand_id.toString() === value.toString());
                    if (brand) {
                        searchInput.value = brand.brand_name || value.toString();
                    } else {
                        searchInput.value = value.toString();
                    }
                }
            }
        }

        // Edit Master Asset Functionality
        const editButtons = document.querySelectorAll('.edit-master-asset-btn');
        const editModal = document.getElementById('editMasterAssetModal');
        const editModalContent = document.getElementById('editMasterAssetModalContent');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editForm = document.getElementById('editMasterAssetForm');
        const editFormSpinner = document.getElementById('editFormSpinner');
        const editFormContent = document.getElementById('edit-form-content');

        // Modal Open/Close functions
        const openModal = function(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        };

        const closeModal = function(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        function closeEditModal() {
            closeModal(editModal, editModalContent);
        }

        function resetEditMasterAssetForm() {
            editForm.reset();

            // Hide image preview if exists
            const imagePreview = document.getElementById('edit_image_preview');
            if (imagePreview) {
                imagePreview.classList.add('hidden');
            }

            // Remove any hidden input for image removal
            const removeImageInput = editForm.querySelector('input[name="remove_image"]');
            if (removeImageInput) {
                removeImageInput.remove();
            }
        }

        // Close modal when clicking on the background
        editModal.addEventListener('mousedown', function(event) {
            if (event.target === this) {
                closeEditModal();
                resetEditMasterAssetForm();
            }
        });

        // Open edit modal when clicking edit button
        editButtons.forEach(button => {
            console.log('Button attached:', button);
            button.addEventListener('click', function() {
                console.log('Button clicked');
                const assetId = this.getAttribute('data-master-asset-id');
                console.log('Asset ID:', assetId);
                if (!assetId) {
                    console.error('No asset ID provided');
                    return;
                }

                // Show modal & spinner first
                editFormSpinner.classList.remove('hidden');
                editFormContent.classList.add('hidden');
                editForm.action = `/asset-master/${assetId}`;
                openModal(editModal, editModalContent);

                // Initialize custom selects
                initCustomSelects();

                // Fetch asset data
                fetchMasterAssetDetails(assetId);
            });
        });

        // Close modal when clicking close button
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeEditModal();
                resetEditMasterAssetForm();
            });
        });

        // Fetch master asset details from the server
        function fetchMasterAssetDetails(assetId) {
            fetch(`/asset-master/${assetId}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.masterAsset) {
                    populateEditForm(data.masterAsset);

                    // Hide spinner, show form
                    editFormSpinner.classList.add('hidden');
                    editFormContent.classList.remove('hidden');
                } else {
                    console.error('Failed to fetch master asset details');
                    closeEditModal();
                    alert('Failed to load master asset details. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error fetching master asset details:', error);
                closeEditModal();
                alert('Failed to load master asset details. Please try again.');
            });
        }

        // Populate the edit form with data
        function populateEditForm(masterAsset) {
            // Basic fields
            document.getElementById('edit_asset_name').value = masterAsset.asset_name || '';
            document.getElementById('edit_asset_type').value = masterAsset.asset_type || '';
            document.getElementById('edit_description').value = masterAsset.description || '';

            // Set subcategory and brand using the helper function
            setSelectValue('edit_subcategory_id', masterAsset.subcategory_id);
            setSelectValue('edit_brand_id', masterAsset.brand_id);

            // Checkboxes with toggle switch display
            const isDepreciable = document.getElementById('edit_is_depreciable');
            isDepreciable.checked = !!masterAsset.is_depreciable;
            document.querySelector('#editMasterAssetModal .depreciation-status').textContent =
                masterAsset.is_depreciable ? 'Yes' : 'No';

            const needsCalibration = document.getElementById('edit_needs_calibration');
            needsCalibration.checked = !!masterAsset.needs_calibration;
            document.querySelector('#editMasterAssetModal .calibration-status').textContent =
                masterAsset.needs_calibration ? 'Yes' : 'No';

            // Image preview
            if (masterAsset.reference_image_path) {
                const imagePreview = document.getElementById('edit_image_preview');
                const currentImage = document.getElementById('edit_current_image');
                imagePreview.classList.remove('hidden');
                currentImage.src = `${window.appConfig.backendUrl}/public${masterAsset.reference_image_path}`;
                currentImage.onerror = function() {
                    this.src = '/images/no-image.png';
                    this.onerror = null;
                };
            } else {
                document.getElementById('edit_image_preview').classList.add('hidden');
            }
        }

        // Handle asset type change to filter subcategories
        document.getElementById('edit_asset_type').addEventListener('change', function() {
            const selectedType = this.value;
            const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
            const options = container.querySelectorAll('.option');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = document.getElementById('edit_subcategory_id');

            // Reset subcategory selection
            searchInput.value = '';
            hiddenInput.value = '';

            // Filter subcategories by asset type
            let hasVisibleOptions = false;
            options.forEach(option => {
                const optionType = option.dataset.type;
                if (!selectedType || optionType === selectedType) {
                    option.style.display = '';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Show a message if no matching subcategories
            const optionsContainer = container.querySelector('.options-container');
            const existingNoResults = optionsContainer.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (!hasVisibleOptions) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'no-results p-3 text-center text-gray-500';
                msgDiv.textContent = 'No subcategories found for this asset type';
                optionsContainer.appendChild(msgDiv);
            }
        });

        // Image change
        document.getElementById('edit_image_file').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const imagePreview = document.getElementById('edit_image_preview');
                    const currentImage = document.getElementById('edit_current_image');

                    currentImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');

                    // If there's a remove image checkbox, uncheck it
                    const removeCheckbox = document.getElementById('edit_remove_image');
                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                    }
                };

                reader.readAsDataURL(file);
            }
        });

        // Handle remove image button
        const removeImageBtns = document.querySelectorAll('.remove-image-btn');
        removeImageBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const imagePreview = document.getElementById('edit_image_preview');
                imagePreview.classList.add('hidden');

                // Clear the file input
                document.getElementById('edit_image_file').value = '';

                // Add a hidden input to signal image removal
                let removeImageInput = editForm.querySelector('input[name="remove_image"]');
                if (!removeImageInput) {
                    removeImageInput = document.createElement('input');
                    removeImageInput.type = 'hidden';
                    removeImageInput.name = 'remove_image';
                    editForm.appendChild(removeImageInput);
                }
                removeImageInput.value = '1';
            });
        });

        // Toggle switches
        document.getElementById('edit_is_depreciable').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .depreciation-status');
            statusText.textContent = this.checked ? 'Yes' : 'No';
        });

        document.getElementById('edit_needs_calibration').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .calibration-status');
            statusText.textContent = this.checked ? 'Yes' : 'No';
        });

        // Initialize back-end URL from meta tag
        window.appConfig = {
            backendUrl: '{{ config('app.backend_url') }}'
        };

        // Initialize custom selects on page load
        initCustomSelects();

        // Tambahkan di bagian akhir script
        window.testModal = function() {
            console.log('Testing modal manually');
            const modal = document.getElementById('editMasterAssetModal');
            const content = document.getElementById('editMasterAssetModalContent');
            modal.style.display = 'block'; // Force display block
            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }
    });
</script>
@endpush
