@extends('Layout.app')

@section('title', 'Asset Detail')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Asset Details Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-xl md:text-2xl lg:text-[32px] font-semibold text-[#213268]">ASSET DETAILS</h1>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto justify-start md:justify-end">
                        <a href="#" class="flex items-center justify-center gap-1 md:gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-[#203268] rounded-lg text-white text-xs md:text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Check Out</span>
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm">Dispose</span>
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm">Lost</span>
                        </a>
                        <a href="javascript:void(0)" id="editAssetBtn" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span class="text-sm">Edit</span>
                        </a>
                    </div>
                </div>

                <!-- Asset Details Main Content -->
                <div class="flex flex-col lg:flex-row gap-4 md:gap-8">
                    <!-- Asset Image and Status -->
                    <div class="w-full lg:w-[350px] xl:w-[400px]">
                        <div class="bg-[#D9D9D9] rounded-[20px] shadow-md h-[180px] md:h-[268px] w-full flex items-center justify-center overflow-hidden relative">
                            @if(isset($asset['picture_path']) && $asset['picture_path'])
                                <img src="http://localhost:5000/public{{ $asset['picture_path'] }}"
                                     alt="Asset Image"
                                     class="absolute inset-0 w-full h-full object-cover p-0"
                                     style="object-position: center;"
                                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4'); this.style.position='relative';">
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-300 rounded-[20px]"></div>
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm">No image available</span>
                                </div>
                            @endif
                        </div>

                        <!-- Asset identification - mobile layout with smaller text -->
                        <div class="flex flex-col items-center mt-4">
                            <p class="text-sm font-medium text-center mt-1">{{ $asset['asset_code'] ?? '-' }}</p>
                            <p class="text-sm font-medium text-center mt-2">{{ $asset['asset_name'] ?? '-' }}</p>
                            @php
                                $statusColor = 'bg-gray-500';
                                if(isset($asset['current_status'])) {
                                    switch(strtolower($asset['current_status'])) {
                                        case 'available':
                                            $statusColor = 'bg-[#659B09]';
                                            break;
                                        case 'in_use':
                                            $statusColor = 'bg-[#F59E0B]';
                                            break;
                                        case 'lost':
                                            $statusColor = 'bg-[#EF4444]';
                                            break;
                                        case 'disposed':
                                            $statusColor = 'bg-[#ACC3EF]';
                                            break;
                                    }
                                }
                            @endphp
                            <div class="{{ $statusColor }} py-0.5 px-3 rounded-md w-full max-w-[120px] text-center mt-2">
                                <p class="text-xs text-white">{{ strtoupper($asset['current_status'] ?? 'UNKNOWN') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Information -->
                    <div class="flex-1 mt-4 lg:mt-0">
                        <h2 class="text-xl font-semibold text-[#203268] mb-4">Asset Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Categories</span>
                                    <span>{{ $asset['subcategory']['asset_type'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Sub Categories</span>
                                    <span>{{ $asset['subcategory']['subcategory_name'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Room</span>
                                    <span>{{ $asset['room']['room_name'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Building</span>
                                    <span>{{ $asset['room']['building']['building_name'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Brand</span>
                                    <span>{{ $asset['brand']['brand_name'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Warranty Date</span>
                                    <span>{{ $asset['warranty_end_date'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Lost Date</span>
                                    <span>
                                        @if($asset['current_status'] === 'lost' && isset($asset['updated_at']))
                                            @php
                                                // Convert the timestamp to a more readable format
                                                $lostDate = \Carbon\Carbon::parse($asset['updated_at'])->format('Y-m-d');
                                            @endphp
                                            {{ $lostDate }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Model Number</span>
                                    <span>{{ $asset['model_number'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Serial Number</span>
                                    <span>{{ $asset['serial_number'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Purchase Cost</span>
                                    <span>{{ number_format((float)($asset['purchase_cost'] ?? 0), 2) }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Purchase Date</span>
                                    <span>{{ $asset['purchase_date'] ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Disposal Date</span>
                                    <span>{{ $asset['current_status'] === 'disposed' ? ($asset['updated_at'] ?? '-') : '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Condition</span>
                                    <span>{{ ucfirst($asset['condition'] ?? '-') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mt-4">
                    <h2 class="text-xl font-semibold text-black mb-4">Description</h2>
                    <p class="text-[#000000]">{{ $asset['description'] ?? '-' }}</p>
                </div>

                <!-- Tabs Section -->
                <div class="mt-4">
                    <!-- Tabs Navigation -->
                    <div class="card bg-base-100 shadow-xl overflow-hidden">
                        <div class="flex flex-nowrap border-b border-[#EEF1F4] bg-white w-full overflow-x-auto hide-scrollbar">
                            <button class="tab-btn whitespace-nowrap flex-none md:flex-1 flex items-center justify-center gap-1 md:gap-2 px-3 py-2 md:px-2 md:py-3 text-[#213268] border-b-2 border-[#213268] font-medium text-xs md:text-sm active" data-tab="document">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Document</span>
                            </button>
                            <button class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]" data-tab="history">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                History
                            </button>
                            <button class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]" data-tab="schedule">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Schedule
                            </button>
                            <button class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]" data-tab="mutation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                Mutation
                            </button>
                            <button class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]" data-tab="depreciation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Depreciation
                            </button>
                            <button class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]" data-tab="scanhistory">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                                Scan History
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div id="tab-content">
                            <div class="tab-pane" id="document">
                                @include('Asset.Tabs.Document')
                            </div>
                            <div class="tab-pane hidden" id="history">
                                @include('Asset.Tabs.History')
                            </div>
                            <div class="tab-pane hidden" id="schedule">
                                @include('Asset.Tabs.Schedule')
                            </div>
                            <div class="tab-pane hidden" id="mutation">
                                @include('Asset.Tabs.Mutation')
                            </div>
                            <div class="tab-pane hidden" id="depreciation">
                                @include('Asset.Tabs.Depreciation')
                            </div>
                            <div class="tab-pane hidden" id="scanhistory">
                                @include('Asset.Tabs.ScanHistory')
                            </div>
                        </div>
                    </div>
                </div>
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

                <!-- Form -->
                <form id="editAssetForm" method="POST" action="{{ route('asset.update', ['id' => $asset['asset_id'] ?? '']) }}" enctype="multipart/form-data">
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
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-blue-600">browse</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                    </div>
                                    <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                                <div id="edit_preview-container" class="mt-2">
                                    <img id="edit_image_preview" class="max-h-40 rounded-lg hidden" alt="Asset Image">
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
                                        @if(isset($subcategories))
                                            @foreach($subcategories as $subcategory)
                                                <option value="{{ $subcategory['subcategory_id'] }}">{{ $subcategory['subcategory_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Condition</label>
                                    <select name="condition" id="edit_condition" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="good">Good</option>
                                        <option value="slighly damage">Slightly Damage</option>
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
                                                {{ $room['room_name'] }} ({{ $room['building']['building_name'] ?? '-' }})
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
       // Tab functionality
       const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        // Show the first tab by default
        if(tabPanes.length > 0) {
            tabPanes.forEach(pane => pane.classList.add('hidden'));
            const firstTab = document.getElementById(tabButtons[0].getAttribute('data-tab'));
            if(firstTab) firstTab.classList.remove('hidden');
        }

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                tabButtons.forEach(button => {
                    button.classList.remove('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');
                    button.classList.add('text-gray-500');
                });

                // Add active class to clicked button
                this.classList.remove('text-gray-500');
                this.classList.add('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');

                // Hide all tab content
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                });

                // Show selected tab content
                const tabName = this.getAttribute('data-tab');
                const selectedTab = document.getElementById(tabName);
                if(selectedTab) selectedTab.classList.remove('hidden');
            });
        });

        // Asset Edit Modal Functionality
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
            }, 300);
        }

        // Setup Edit button
        const editBtn = document.getElementById('editAssetBtn');
        const editModal = document.getElementById('editAssetModal');
        const editModalContent = document.getElementById('editAssetModalContent');

        if (editBtn && editModal && editModalContent) {
            editBtn.addEventListener('click', function() {
                // Get asset ID from current page
                const assetId = '{{ $asset["asset_id"] ?? "" }}';

                if (!assetId) {
                    console.error('Asset ID not found');
                    return;
                }

                // Show loading state
                editBtn.classList.add('opacity-50', 'pointer-events-none');

                // Fetch asset data including subcategories, rooms, and brands from server
                fetch(`{{ route('assets.get', '') }}/${assetId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Reset loading state
                    editBtn.classList.remove('opacity-50', 'pointer-events-none');

                    if (data.error) {
                        console.error('Error fetching asset:', data.error);
                        alert('Failed to load asset data: ' + data.error);
                        return;
                    }

                    // Populate dropdowns with the fetched data
                    populateSubcategories(data.subcategories);
                    populateRooms(data.rooms);
                    populateBrands(data.brands);

                    // Set up the edit form with the data
                    setupEditAssetForm(data.asset);

                    // Open the modal
                    openModal(editModal, editModalContent);
                })
                .catch(error => {
                    // Reset loading state
                    editBtn.classList.remove('opacity-50', 'pointer-events-none');
                    console.error('Error fetching asset:', error);
                    alert('Failed to load asset data. Please try again.');
                });
            });
        }

        // Setup close buttons
        const closeButtons = document.querySelectorAll('.close-modal');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        // Close on outside click
        if (editModal) {
            editModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(editModal, editModalContent);
                }
            });
        }

        // Handle depreciation toggle
        const depreciableToggle = document.getElementById('edit_is_depreciable');
        const depreciationFields = document.getElementById('edit_depreciation_fields');

        if (depreciableToggle && depreciationFields) {
            depreciableToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                const statusText = document.querySelector('.depreciation-status');

                if (statusText) {
                    statusText.textContent = isChecked ? 'Yes' : 'No';
                }

                if (isChecked) {
                    depreciationFields.classList.remove('hidden');

                    // Enable input fields
                    const inputs = depreciationFields.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.disabled = false;
                        input.classList.remove('bg-gray-100');
                    });
                } else {
                    depreciationFields.classList.add('hidden');

                    // Disable input fields
                    const inputs = depreciationFields.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('bg-gray-100');
                    });
                }
            });
        }

        // Add this event listener to handle automatic acquisition cost update when enabling depreciation
        if (depreciableToggle) {
            depreciableToggle.addEventListener('change', function() {
                // If depreciation is being enabled, set acquisition cost to match purchase cost
                if (this.checked) {
                    const purchaseCost = document.getElementById('edit_purchase_cost').value || '0';
                    document.getElementById('edit_acquisition_cost').value = purchaseCost;

                    // Also set date acquired to match purchase date if available
                    const purchaseDate = document.getElementById('edit_purchase_date').value;
                    if (purchaseDate) {
                        document.getElementById('edit_date_acquired').value = purchaseDate;
                    }
                }
            });
        }

        // Also update acquisition cost whenever purchase cost changes
        document.getElementById('edit_purchase_cost').addEventListener('input', function() {
            // Only update if depreciation is enabled
            if (document.getElementById('edit_is_depreciable').checked) {
                document.getElementById('edit_acquisition_cost').value = this.value;
            }
        });

        // Handle image preview
        const fileInput = document.getElementById('edit_image_file');
        const imagePreview = document.getElementById('edit_image_preview');

        if (fileInput && imagePreview) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Function to set up the edit form with data
        window.setupEditAssetForm = function(asset) {
            console.log('Setting up edit asset form with data:', asset);

            // Get form element
            const form = document.getElementById('editAssetForm');
            if (!form) {
                console.error('Edit asset form not found');
                return;
            }

            // Set form action
            form.action = `{{ route('asset.update', '') }}/${asset.asset_id}`;

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

            // Set dropdown values
            setSelectValue('edit_subcategory_id', asset.subcategory?.subcategory_id);
            setSelectValue('edit_room_id', asset.room?.room_id);
            setSelectValue('edit_brand_id', asset.brand?.brand_id);
            setSelectValue('edit_condition', asset.condition);

            // Set depreciation toggle
            const depreciableToggle = document.getElementById('edit_is_depreciable');
            const depreciationStatus = document.querySelector('.depreciation-status');
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
                    const inputs = depreciationFields.querySelectorAll('input, select');
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
                    const inputs = depreciationFields.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('bg-gray-100');
                    });
                }
            }

            // Handle image preview if available
            if (asset.picture_path) {
                const imagePreview = document.getElementById('edit_image_preview');

                if (imagePreview) {
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

                    // Set the image source and display it
                    imagePreview.src = imageUrl;
                    imagePreview.classList.remove('hidden');
                }
            } else {
                // Hide the preview if no image
                const imagePreview = document.getElementById('edit_image_preview');
                if (imagePreview) {
                    imagePreview.classList.add('hidden');
                }
            }
        }

        // Helper function to set dropdown values
        function setSelectValue(selectId, value) {
            const select = document.getElementById(selectId);
            if (!select || value === undefined || value === null) {
                return;
            }

            // Convert to string for comparison
            const valueStr = String(value);

            // Find the matching option
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === valueStr) {
                    select.selectedIndex = i;
                    return;
                }
            }
        }

        // Helper functions to populate dropdowns
        function populateSubcategories(subcategories) {
            const select = document.getElementById('edit_subcategory_id');
            if (!select || !subcategories) return;

            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add new options
            subcategories.forEach(subcategory => {
                const option = document.createElement('option');
                option.value = subcategory.subcategory_id;
                option.textContent = subcategory.subcategory_name;
                select.appendChild(option);
            });
        }

        // Add these helper functions for populating dropdown menus
        function populateRooms(rooms) {
            const select = document.getElementById('edit_room_id');
            if (!select || !rooms) return;

            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add new options
            rooms.forEach(room => {
                const option = document.createElement('option');
                option.value = room.room_id;
                option.textContent = `${room.room_name} (${room.building?.building_name || '-'})`;
                select.appendChild(option);
            });
        }

        function populateBrands(brands) {
            const select = document.getElementById('edit_brand_id');
            if (!select || !brands) return;

            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add new options
            brands.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand.brand_id;
                option.textContent = brand.brand_name;
                select.appendChild(option);
            });
        }
    });
</script>
@endpush
