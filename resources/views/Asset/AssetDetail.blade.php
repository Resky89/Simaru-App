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
                        <a href="#" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
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
                        <div class="bg-[#D9D9D9] rounded-[20px] shadow-md h-[180px] md:h-[268px] w-full flex items-center justify-center">
                            <!-- Asset image would go here -->
                            <img src="/path/to/asset-image.jpg" alt="Asset Image" class="hidden">
                        </div>

                        <!-- Asset identification - mobile layout with smaller text -->
                        <div class="flex flex-col items-center mt-4">
                            <p class="text-sm font-medium text-center mt-1">PM-4380</p>
                            <p class="text-sm font-medium text-center mt-2">Asset Name</p>
                            <div class="bg-[#659B09] py-0.5 px-3 rounded-md w-full max-w-[120px] text-center mt-2">
                                <p class="text-xs text-white">AVAILABLE</p>
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
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Sub Categories</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Location</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Brand</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Warranty Date</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Lost Date</span>
                                    <span>-</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Model Number</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Serial Number</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Purchase Cost</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Purchase Date</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Disposal Date</span>
                                    <span>-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[140px] font-semibold">Condition</span>
                                    <span>-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mt-4">
                    <h2 class="text-xl font-semibold text-black mb-4">Description</h2>
                    <p class="text-[#000000]">-</p>
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

    <!-- Add Document Modal -->
    <div id="addDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div id="documentModalContent" class="bg-white rounded-lg shadow-xl w-full max-w-md transform scale-95 opacity-0 translate-y-4 transition-all duration-300">
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#213268]">Add Document</h3>
                    <button class="close-modal text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Document Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]" rows="4"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                        <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#1a2855]">Upload</button>
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

        const addDocumentBtn = document.getElementById('addDocumentBtn');
        const addDocumentModal = document.getElementById('addDocumentModal');
        const documentModalContent = document.getElementById('documentModalContent');
        const closeModalBtns = document.querySelectorAll('.close-modal');

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

        if (addDocumentBtn) {
            addDocumentBtn.addEventListener('click', function() {
                openModal(addDocumentModal, documentModalContent);
            });
        }

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('.fixed.inset-0');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
            });
        });

        // Handle click outside modal
        addDocumentModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this, documentModalContent);
            }
        });
    });
</script>
@endpush
