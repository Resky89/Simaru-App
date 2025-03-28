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
                        <a href="" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
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
                                            $statusColor = 'bg-[#6B7280]';
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
    });
</script>
@endpush
