@extends('Layout.app')

@section('title', 'Formulir Permintaan')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.request') }}" id="backButton" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORMULIR PERMINTAAN</h1>
                    </div>
                </div>

                <!-- Form -->
                <form id="requestForm" class="w-full space-y-6">
                    @csrf
                    <input type="hidden" id="procurement_id" name="procurement_id">

                    <!-- Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Judul Permintaan</label>
                        <input type="text" id="title" name="title"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Judul Permintaan" required>
                    </div>

                    <!-- Priority  -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Prioritas</label>
                        <select id="priority" name="priority"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            required>
                            <option value="" disabled selected>Pilih prioritas</option>
                            <option value="High">Tinggi</option>
                            <option value="Medium">Sedang</option>
                            <option value="Low">Rendah</option>
                        </select>
                    </div>

                     <!-- Justification -->
                     <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Justifikasi</label>
                        <textarea id="justification" name="justification"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Justifikasi" rows="3" required></textarea>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">Daftar Aset</label>

                        <div id="itemContainer" class="space-y-6">
                            <div class="item-entry p-6 border border-[#CCCCCC] rounded-lg relative">
                                <!-- Delete Button (On Border) - Initially hidden for first item -->
                                <button type="button" class="remove-item-btn absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200 shadow-md z-10 hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Asset Selection Type -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Tipe Aset</label>
                                        <select class="asset-type-selector w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                            <option value="new">Aset Baru</option>
                                            <option value="existing">Aset yang Ada</option>
                                        </select>
                                    </div>

                                    <!-- Item Name (for new assets) -->
                                    <div class="space-y-2 asset-name-container">
                                        <label class="block text-sm font-medium text-[#666666]">Nama Aset</label>
                                        <input type="text" name="details[0][asset_name]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                                            placeholder="Nama Aset" required>
                                    </div>

                                    <!-- Asset Master Selection (for existing assets) - initially hidden -->
                                    <div class="space-y-2 asset-master-container hidden">
                                        <label class="block text-sm font-medium text-[#666666]">Pilih Aset yang Ada</label>
                                        <div class="relative">
                                            <input type="text" class="asset-master-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Cari aset..." autocomplete="off">
                                            <input type="hidden" name="details[0][asset_master_id]" class="asset-master-id">

                                            <!-- Dropdown -->
                                            <div class="asset-master-dropdown absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div class="asset-master-loading p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>Memuat daftar aset...</span>
                                                </div>
                                                <ul class="asset-master-list py-1"></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Jumlah</label>
                                        <input type="number" name="details[0][quantity]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                                            placeholder="Jumlah" min="1" required>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Harga Satuan</label>
                                        <input type="number" name="details[0][estimated_unit_price]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                            placeholder="Harga Satuan" min="0" required>
                                    </div>
                                </div>

                                <!-- Specifications -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-[#666666]">Spesifikasi</label>
                                    <textarea name="details[0][specifications]"
                                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                                        placeholder="Spesifikasi" rows="2"></textarea>
                                </div>

                                <!-- Notes -->
                                <div class="space-y-2 mt-4">
                                    <label class="block text-sm font-medium text-[#666666]">Catatan</label>
                                    <textarea name="details[0][notes]"
                                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 notes"
                                        placeholder="Catatan (opsional)" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Add Item Button -->
                        <div class="flex justify-end">
                            <button type="button" id="addItemBtn" class="w-12 h-12 rounded-full bg-[#213268] text-white flex items-center justify-center shadow-lg hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <button type="submit" id="submitButton" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            KIRIM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container - Will be populated dynamically -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white p-6 rounded-lg shadow-xl z-10 w-full max-w-md">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Berhasil!</h3>
            <p class="mt-2 text-sm text-gray-500" id="successMessage">Permintaan Anda telah berhasil dikirim.</p>
            <div class="mt-4">
                <button id="successModalClose" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#152451]">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white p-6 rounded-lg shadow-xl z-10 w-full max-w-md">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Kesalahan!</h3>
            <p class="mt-2 text-sm text-gray-500" id="errorMessage">Terjadi kesalahan. Silakan coba lagi.</p>
            <div class="mt-4">
                <button id="errorModalClose" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addItemBtn = document.getElementById('addItemBtn');
        const itemContainer = document.getElementById('itemContainer');
        const requestForm = document.getElementById('requestForm');
        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const successModalClose = document.getElementById('successModalClose');
        const errorModalClose = document.getElementById('errorModalClose');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Add a flag to track if we're currently submitting/redirecting to prevent unwanted navigation
        let isNavigatingAway = false;

        // Show warning when attempting to leave the page with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            // Only show if there are form changes and we're not already submitting or redirecting
            if (!isSubmitting && !isNavigatingAway && formHasChanges()) {
                // Standard text (browsers may override this message)
                const message = 'Perubahan yang Anda buat mungkin tidak disimpan.';
                e.returnValue = message;
                return message;
            }
        });

        // Helper function to check if the form has any changes
        function formHasChanges() {
            // Check required fields
            if (document.getElementById('title').value ||
                document.getElementById('priority').value ||
                document.getElementById('justification').value) {
                return true;
            }

            // Check if any item details have been entered
            const items = itemContainer.querySelectorAll('.item-entry');
            for (const item of items) {
                const inputs = item.querySelectorAll('input, textarea, select');
                for (const input of inputs) {
                    if (input.value && input.name !== '') {
                        return true;
                    }
                }
            }

            return false;
        }

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Array to store asset master data
        let assetMasters = [];

        // Track selected asset master IDs
        let selectedAssetMasterIds = new Set();

        // Preload asset masters when page loads
        fetchAssetMasters().then(() => {
            console.log('Asset masters preloaded');
        });

        // Initialize asset type selectors
        addAssetTypeSelectorListeners();

        // Function to get currently selected asset master IDs
        function updateSelectedAssetMasterIds() {
            selectedAssetMasterIds.clear();
            document.querySelectorAll('.asset-master-id').forEach(input => {
                if (input.value) {
                    selectedAssetMasterIds.add(parseInt(input.value));
                }
            });
            console.log('Selected asset master IDs:', Array.from(selectedAssetMasterIds));
        }

        // Fetch asset masters for dropdowns
        function fetchAssetMasters() {
            // If we already have asset masters, no need to fetch again
            if (assetMasters.length > 0) {
                updateAssetMasterDropdowns();
                return Promise.resolve(assetMasters);
            }

            // Show loading indicator in all dropdowns
            document.querySelectorAll('.asset-master-loading').forEach(loading => {
                loading.style.display = 'block';
            });

            // Use the route that's working in UnitAsset.blade.php
            return fetch('{{ route("asset-master.data") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // This format matches what's in UnitAsset.blade.php
                    assetMasters = data.masterAssets || [];
                    console.log('Loaded', assetMasters.length, 'asset masters');

                    // Update selected asset master IDs
                    updateSelectedAssetMasterIds();

                    // Update all asset master dropdowns
                    updateAssetMasterDropdowns();

                    // Hide all loading indicators
                    document.querySelectorAll('.asset-master-loading').forEach(loading => {
                        loading.style.display = 'none';
                    });

                    return assetMasters;
                })
                .catch(error => {
                    console.error('Error fetching asset masters:', error);

                    // Hide all loading indicators on error
                    document.querySelectorAll('.asset-master-loading').forEach(loading => {
                        loading.style.display = 'none';
                    });

                    // Show error in the dropdown
                    document.querySelectorAll('.asset-master-list').forEach(list => {
                        const errorItem = document.createElement('li');
                        errorItem.className = 'px-4 py-2 text-red-500';
                        errorItem.textContent = 'Gagal memuat daftar aset';
                        list.appendChild(errorItem);
                    });

                    return [];
                });
        }

        // Update all asset master dropdowns with options
        function updateAssetMasterDropdowns() {
            // First, update the list of selected asset master IDs
            updateSelectedAssetMasterIds();

            // Process each dropdown
            document.querySelectorAll('.asset-master-list').forEach(listElement => {
                // Get the current entry's asset master ID
                const currentContainer = listElement.closest('.asset-master-container');
                const currentHiddenInput = currentContainer.querySelector('.asset-master-id');
                const currentAssetMasterId = currentHiddenInput.value ? parseInt(currentHiddenInput.value) : null;

                // Clear previous items
                listElement.innerHTML = '';

                // Filter assets to exclude selected ones except the current selection
                const availableAssets = assetMasters.filter(asset => {
                    const assetId = parseInt(asset.asset_master_id);
                    // Include if not selected or if it's the current selection
                    return !selectedAssetMasterIds.has(assetId) || (currentAssetMasterId === assetId);
                });

                // Add options for each available asset master
                availableAssets.forEach(asset => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                    li.textContent = asset.asset_name || 'Aset tidak dikenal';
                    li.setAttribute('data-id', asset.asset_master_id);
                    li.setAttribute('data-name', asset.asset_name || 'Aset tidak dikenal');

                    // Add click handler
                    li.addEventListener('click', function() {
                        const container = this.closest('.asset-master-container');
                        const searchInput = container.querySelector('.asset-master-search');
                        const hiddenInput = container.querySelector('.asset-master-id');
                        const dropdown = container.querySelector('.asset-master-dropdown');

                        // Set values
                        searchInput.value = this.getAttribute('data-name');
                        hiddenInput.value = this.getAttribute('data-id');

                        // Hide dropdown
                        dropdown.classList.add('hidden');

                        // Update selected asset master IDs and refresh all dropdowns
                        updateSelectedAssetMasterIds();
                        updateAssetMasterDropdowns();
                    });

                    listElement.appendChild(li);
                });

                // Show "No results" if empty
                if (availableAssets.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-4 py-2 text-gray-500 italic';
                    noResults.textContent = 'Tidak ada aset tersedia';
                    listElement.appendChild(noResults);
                }
            });
        }

        // Setup asset master search functionality for a specific container
        function setupAssetMasterSearch(container) {
            const searchInput = container.querySelector('.asset-master-search');
            const dropdown = container.querySelector('.asset-master-dropdown');
            const list = container.querySelector('.asset-master-list');
            const loadingIndicator = container.querySelector('.asset-master-loading');
            const hiddenInput = container.querySelector('.asset-master-id');

            if (searchInput && dropdown && list) {
                // When input is cleared, clear the hidden value too
                searchInput.addEventListener('input', function() {
                    if (!this.value.trim()) {
                        hiddenInput.value = '';
                        // Update available options for all dropdowns
                        updateSelectedAssetMasterIds();
                        updateAssetMasterDropdowns();
                    }
                });

                // Show dropdown on focus
                searchInput.addEventListener('focus', function() {
                    dropdown.classList.remove('hidden');

                    // If we already have asset masters, populate the dropdown without showing loading
                    if (assetMasters.length > 0) {
                        loadingIndicator.style.display = 'none';
                        updateAssetMasterDropdowns();
                    } else {
                        // Otherwise fetch asset masters
                        loadingIndicator.style.display = 'block';
                        fetchAssetMasters().then(() => {
                            loadingIndicator.style.display = 'none';
                        });
                    }
                });

                // Filter items on input
                searchInput.addEventListener('input', function() {
                    const value = this.value.toLowerCase();
                    const items = list.querySelectorAll('li');

                    let hasVisibleItems = false;

                    items.forEach(item => {
                        if (item.classList.contains('no-results-item')) {
                            item.remove();
                        } else {
                            const text = item.textContent.toLowerCase();
                            if (text.includes(value)) {
                                item.style.display = '';
                                hasVisibleItems = true;
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });

                    // Show "No results" message if needed
                    if (!hasVisibleItems) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic no-results-item';
                        noResults.textContent = 'Tidak ada aset yang cocok';
                        list.appendChild(noResults);
                    }
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        }

        // Get procurement ID from URL if present (for edit mode)
        const urlParams = new URLSearchParams(window.location.search);
        const procurementId = urlParams.get('id');

        // If procurement ID exists, we're in edit mode
        if (procurementId) {
            // Set the hidden procurement_id field
            document.getElementById('procurement_id').value = procurementId;

            // Load procurement data
            loadProcurementData(procurementId);
        }

        // Function to handle asset type selection change
        function handleAssetTypeChange(selector, itemEntry) {
            const assetType = selector.value;
            const assetNameContainer = itemEntry.querySelector('.asset-name-container');
            const assetMasterContainer = itemEntry.querySelector('.asset-master-container');
            const assetNameInput = itemEntry.querySelector('.asset-name');
            const assetMasterInput = itemEntry.querySelector('.asset-master-id');

            if (assetType === 'new') {
                assetNameContainer.classList.remove('hidden');
                assetMasterContainer.classList.add('hidden');
                assetNameInput.disabled = false;
                assetMasterInput.disabled = true;
                assetNameInput.required = true;
                assetMasterInput.required = false;

                // Clear asset master selection
                assetMasterInput.value = '';
                const assetMasterSearch = assetMasterContainer.querySelector('.asset-master-search');
                if (assetMasterSearch) {
                    assetMasterSearch.value = '';
                }

                // Update dropdown availabilities
                updateSelectedAssetMasterIds();
                updateAssetMasterDropdowns();
            } else {
                assetNameContainer.classList.add('hidden');
                assetMasterContainer.classList.remove('hidden');
                assetNameInput.disabled = true;
                assetMasterInput.disabled = false;
                assetNameInput.required = false;
                assetMasterInput.required = true;

                // Initialize the asset master search if not already done
                setupAssetMasterSearch(assetMasterContainer);

                // Update dropdown to show available options
                updateAssetMasterDropdowns();
            }
        }

        // Add event listeners to all asset type selectors
        function addAssetTypeSelectorListeners() {
            document.querySelectorAll('.asset-type-selector').forEach(selector => {
                if (!selector.hasEventListener) {
                    selector.hasEventListener = true;
                    selector.addEventListener('change', function() {
                        const itemEntry = this.closest('.item-entry');
                        handleAssetTypeChange(this, itemEntry);
                    });
                }
            });
        }

        // Function to load existing procurement data
        function loadProcurementData(id) {
            fetch(`/procurement/request/${id}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data permintaan');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const procurement = data.data;

                    // Fill in basic info
                    document.getElementById('title').value = procurement.title;
                    document.getElementById('priority').value = procurement.priority;
                    document.getElementById('justification').value = procurement.justification;

                    // Clear existing item entries
                    itemContainer.innerHTML = '';

                    // Add item entries for each detail
                    procurement.details.forEach((detail, index) => {
                        addItemEntry(index, detail);
                    });

                    // Update delete buttons visibility
                    updateDeleteButtons();

                    // Initialize asset type selectors for loaded entries
                    addAssetTypeSelectorListeners();

                    // Set the correct asset type based on data
                    document.querySelectorAll('.item-entry').forEach((entry, index) => {
                        const detail = procurement.details[index];
                        const selector = entry.querySelector('.asset-type-selector');

                        if (detail.asset_master_id) {
                            selector.value = 'existing';

                            // Set the selected asset master
                            const assetMasterContainer = entry.querySelector('.asset-master-container');
                            const assetMasterSearch = assetMasterContainer.querySelector('.asset-master-search');
                            const assetMasterId = assetMasterContainer.querySelector('.asset-master-id');

                            if (assetMasterSearch && assetMasterId) {
                                assetMasterId.value = detail.asset_master_id;

                                // Try to find the asset name in our loaded asset masters
                                const asset = assetMasters.find(a => a.asset_master_id == detail.asset_master_id);
                                if (asset) {
                                    assetMasterSearch.value = asset.asset_name;
                                } else {
                                    // Fallback to the detail's asset name
                                    assetMasterSearch.value = detail.asset_name || 'Aset #' + detail.asset_master_id;
                                }

                                // Initialize the asset master search
                                setupAssetMasterSearch(assetMasterContainer);
                            }
                        } else {
                            selector.value = 'new';
                        }

                        // Trigger change event to update UI
                        handleAssetTypeChange(selector, entry);
                    });

                    // Update selected asset master IDs and refresh dropdowns
                    updateSelectedAssetMasterIds();
                    updateAssetMasterDropdowns();
                } else {
                    showToast('Gagal memuat data permintaan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal memuat data permintaan: ' + error.message, 'error');
            });
        }

        // Function to update delete buttons visibility
        function updateDeleteButtons() {
            const items = itemContainer.querySelectorAll('.item-entry');

            // If there's only one item, hide its delete button
            if (items.length === 1) {
                items[0].querySelector('.remove-item-btn').classList.add('hidden');
            } else {
                // Show delete buttons for all items
                items.forEach(item => {
                    item.querySelector('.remove-item-btn').classList.remove('hidden');
                });
            }
        }

        // Add new item entry
        function addItemEntry(index, data = null) {
            const newItem = document.createElement('div');
            newItem.className = 'item-entry p-6 border border-[#CCCCCC] rounded-lg relative';
            newItem.innerHTML = `
                <!-- Delete Button (On Border) -->
                <button type="button" class="remove-item-btn absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200 shadow-md z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Asset Selection Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Tipe Aset</label>
                        <select class="asset-type-selector w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="new">Aset Baru</option>
                            <option value="existing">Aset yang Ada</option>
                        </select>
                    </div>

                    <!-- Item Name (for new assets) -->
                    <div class="space-y-2 asset-name-container">
                        <label class="block text-sm font-medium text-[#666666]">Nama Aset</label>
                        <input type="text" name="details[${index}][asset_name]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                            placeholder="Nama Aset" required value="${data && data.asset_name ? data.asset_name : ''}">
                    </div>

                    <!-- Asset Master Selection (for existing assets) - initially hidden -->
                    <div class="space-y-2 asset-master-container hidden">
                        <label class="block text-sm font-medium text-[#666666]">Pilih Aset yang Ada</label>
                        <div class="relative">
                            <input type="text" class="asset-master-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Cari aset..." autocomplete="off">
                            <input type="hidden" name="details[${index}][asset_master_id]" class="asset-master-id">

                            <!-- Dropdown -->
                            <div class="asset-master-dropdown absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                <div class="asset-master-loading p-2 text-gray-500 text-center" style="display: none;">
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memuat daftar aset...</span>
                                </div>
                                <ul class="asset-master-list py-1"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Jumlah</label>
                        <input type="number" name="details[${index}][quantity]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                            placeholder="Jumlah" min="1" required value="${data ? data.quantity : ''}">
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Harga Satuan</label>
                        <input type="number" name="details[${index}][estimated_unit_price]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                            placeholder="Harga Satuan" min="0" required value="${data ? data.estimated_unit_price : ''}">
                    </div>
                </div>

                <!-- Specifications -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#666666]">Spesifikasi</label>
                    <textarea name="details[${index}][specifications]"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                        placeholder="Spesifikasi" rows="2">${data ? data.specifications || '' : ''}</textarea>
                </div>

                <!-- Notes -->
                <div class="space-y-2 mt-4">
                    <label class="block text-sm font-medium text-[#666666]">Catatan</label>
                    <textarea name="details[${index}][notes]"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 notes"
                        placeholder="Catatan (opsional)" rows="2">${data ? data.notes || '' : ''}</textarea>
                </div>
            `;

            itemContainer.appendChild(newItem);

            // Add event listener to the new remove button
            const removeBtn = newItem.querySelector('.remove-item-btn');
            removeBtn.addEventListener('click', function() {
                newItem.remove();
                // Update delete buttons after removing an item
                updateDeleteButtons();
                // Update input names
                updateInputNames();
                // Update selected asset master IDs and refresh dropdowns
                updateSelectedAssetMasterIds();
                updateAssetMasterDropdowns();
            });

            // Add event listener to the new asset type selector
            const assetTypeSelector = newItem.querySelector('.asset-type-selector');
            assetTypeSelector.addEventListener('change', function() {
                handleAssetTypeChange(this, newItem);
            });

            // Setup asset master search for the new item
            const assetMasterContainer = newItem.querySelector('.asset-master-container');
            setupAssetMasterSearch(assetMasterContainer);

            // If data has asset_master_id, set to existing asset mode
            if (data && data.asset_master_id) {
                assetTypeSelector.value = 'existing';
                handleAssetTypeChange(assetTypeSelector, newItem);

                // Set the asset master ID
                const assetMasterId = newItem.querySelector('.asset-master-id');
                if (assetMasterId) {
                    assetMasterId.value = data.asset_master_id;
                }

                // Set the asset master search display value
                const assetMasterSearch = newItem.querySelector('.asset-master-search');
                if (assetMasterSearch) {
                    // Find the asset name in our loaded asset masters
                    const asset = assetMasters.find(a => a.asset_master_id == data.asset_master_id);
                    if (asset) {
                        assetMasterSearch.value = asset.asset_name;
                    } else {
                        // Fallback to the asset name in the data
                        assetMasterSearch.value = data.asset_name || 'Aset #' + data.asset_master_id;
                    }
                }
            }

            // Update selected asset master IDs after adding a new item
            updateSelectedAssetMasterIds();
        }

        // Add new item when clicking the add button
        addItemBtn.addEventListener('click', function() {
            const itemCount = itemContainer.querySelectorAll('.item-entry').length;
            addItemEntry(itemCount);
            // Update delete buttons after adding a new item
            updateDeleteButtons();
            // Add listeners to new asset type selector
            addAssetTypeSelectorListeners();

            // Populate the asset master dropdowns with already loaded data
            if (assetMasters.length > 0) {
                updateAssetMasterDropdowns();
            }
        });

        // Function to update input names after removing items
        function updateInputNames() {
            const items = itemContainer.querySelectorAll('.item-entry');
            items.forEach((item, index) => {
                const assetName = item.querySelector('.asset-name');
                const assetMasterId = item.querySelector('.asset-master-id');
                const quantity = item.querySelector('.quantity');
                const unitPrice = item.querySelector('.unit-price');
                const specifications = item.querySelector('.specifications');
                const notes = item.querySelector('.notes');

                assetName.name = `details[${index}][asset_name]`;
                assetMasterId.name = `details[${index}][asset_master_id]`;
                quantity.name = `details[${index}][quantity]`;
                unitPrice.name = `details[${index}][estimated_unit_price]`;
                specifications.name = `details[${index}][specifications]`;
                notes.name = `details[${index}][notes]`;
            });
        }

        // Show success modal
        function showSuccess(message) {
            successMessage.textContent = message;
            successModal.classList.remove('hidden');
        }

        // Show error modal
        function showError(message) {
            errorMessage.textContent = message;
            errorModal.classList.remove('hidden');
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Check if message contains HTML
            const hasHTML = /<[a-z][\s\S]*>/i.test(message);

            // Create notification element
            const toast = document.createElement('div');
            toast.className = 'p-4 rounded shadow-md animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';

            if (type === 'success') {
                toast.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');

                // Set content for success toast
                toast.innerHTML = `
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
                toast.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');

                // Structure for error notification
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
                title.textContent = 'Gagal!';
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
                    toast.remove();
                };

                // Assemble the notification
                wrapper.appendChild(iconContainer);
                wrapper.appendChild(contentContainer);
                wrapper.appendChild(closeBtn);
                toast.appendChild(wrapper);
            }

            // Add to container
            toastContainer.appendChild(toast);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Add slide-in animation and styling for error messages
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

                /* Field validation */
                .field-error {
                    border-color: #f56565 !important;
                    box-shadow: 0 0 0 1px #f56565 !important;
                }

                .error-text {
                    color: #f56565;
                    font-size: 0.875rem;
                    margin-top: 0.25rem;
                }
            </style>
        `);

        // Function to validate a form field
        function validateField(field) {
            if (field.tagName.toLowerCase() === 'select') {
                if (!field.value) {
                    field.classList.add('field-error');
                    return false;
                } else {
                    field.classList.remove('field-error');
                    return true;
                }
            } else {
                if (!field.value.trim()) {
                    field.classList.add('field-error');
                    return false;
                } else {
                    field.classList.remove('field-error');
                    return true;
                }
            }
        }

        // Add input/change event listeners to clear error styling on fields
        document.getElementById('title').addEventListener('input', function() {
            this.classList.remove('field-error');
        });

        document.getElementById('priority').addEventListener('change', function() {
            this.classList.remove('field-error');
        });

        document.getElementById('justification').addEventListener('input', function() {
            this.classList.remove('field-error');
        });

        // Close success modal and redirect
        successModalClose.addEventListener('click', function() {
            successModal.classList.add('hidden');
            window.location.href = '{{ route("procurement.request") }}';
        });

        // Close error modal
        errorModalClose.addEventListener('click', function() {
            errorModal.classList.add('hidden');
        });

        // Initialize delete buttons visibility
        updateDeleteButtons();

        // Handle form submission
        let isSubmitting = false; // Flag to track submission status
        requestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Prevent multiple submissions
            if (isSubmitting) {
                return;
            }

            // Disable the submit button and set submitting flag
            isSubmitting = true;
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                PROSES...
            `;

            // Collect form data
            const formData = new FormData(requestForm);
            const data = {
                details: []
            };

            // Simple fields
            data.title = formData.get('title');
            data.priority = formData.get('priority');
            data.justification = formData.get('justification');

            // Process all item entries to create proper details array
            const items = itemContainer.querySelectorAll('.item-entry');
            items.forEach((item, index) => {
                const detailObj = {};
                const assetTypeSelector = item.querySelector('.asset-type-selector');

                // Handle asset type (new or existing)
                if (assetTypeSelector.value === 'new') {
                    detailObj.asset_name = item.querySelector('.asset-name').value;
                } else {
                    const assetMasterId = Number(item.querySelector('.asset-master-id').value);
                    if (assetMasterId) {
                        detailObj.asset_master_id = assetMasterId;
                    } else {
                        // Skip invalid entries
                        return;
                    }
                }

                // Add other required fields
                detailObj.quantity = Number(item.querySelector('.quantity').value);
                detailObj.estimated_unit_price = Number(item.querySelector('.unit-price').value);

                // Add optional fields if they have value
                const specs = item.querySelector('.specifications').value;
                if (specs) detailObj.specifications = specs;

                const notes = item.querySelector('.notes').value;
                if (notes) detailObj.notes = notes;

                // Add to details array
                data.details.push(detailObj);
            });

            // Determine if this is a create or update operation
            const isUpdate = procurementId ? true : false;
            const url = isUpdate
                ? `/procurement/request/${procurementId}`
                : '/procurement/request';
            const method = isUpdate ? 'PUT' : 'POST';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send the request
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showToast(isUpdate
                        ? 'Pengadaan berhasil diperbarui'
                        : 'Pengadaan berhasil dibuat', 'success');

                    // Use single flag to control redirection
                    // Don't re-enable button on success, we're redirecting

                    // Set flag to indicate we're navigating away intentionally
                    isNavigatingAway = true;

                    // Redirect after a short delay to allow for toast to be seen
                    setTimeout(() => {
                        window.location.href = '{{ route("procurement.request") }}';
                    }, 1500);
                } else {
                    // Reset submission status and re-enable the button
                    isSubmitting = false;
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;

                    const errorData = result.errors || {};

                    // Initialize error message
                    let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda:';

                    // Check if errors is an object with field-specific errors
                    if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                        errorMessage += '<ul class="mt-2 list-disc pl-5">';

                        // Process each error field and highlight form fields
                        Object.entries(errorData).forEach(([field, errors]) => {
                            // Highlight field with error
                            const fieldElement = document.getElementById(field);
                            if (fieldElement) {
                                fieldElement.classList.add('field-error');
                            }

                            // Handle detail fields with array notation (e.g., details.0.asset_name)
                            if (field.includes('details.')) {
                                const parts = field.split('.');
                                if (parts.length >= 3) {
                                    const index = parseInt(parts[1]);
                                    const subField = parts[2];

                                    // Find and highlight the field in the specific item entry
                                    const items = itemContainer.querySelectorAll('.item-entry');
                                    if (items[index]) {
                                        let fieldSelector;

                                        switch (subField) {
                                            case 'asset_name':
                                                fieldSelector = '.asset-name';
                                                break;
                                            case 'asset_master_id':
                                                fieldSelector = '.asset-master-search';
                                                break;
                                            case 'quantity':
                                                fieldSelector = '.quantity';
                                                break;
                                            case 'estimated_unit_price':
                                                fieldSelector = '.unit-price';
                                                break;
                                            case 'specifications':
                                                fieldSelector = '.specifications';
                                                break;
                                            case 'notes':
                                                fieldSelector = '.notes';
                                                break;
                                        }

                                        if (fieldSelector) {
                                            const field = items[index].querySelector(fieldSelector);
                                            if (field) {
                                                field.classList.add('field-error');
                                            }
                                        }
                                    }
                                }
                            }

                            if (Array.isArray(errors)) {
                                // Multiple errors for this field
                                errors.forEach(err => {
                                    errorMessage += `<li>${err}</li>`;
                                });
                            } else if (typeof errors === 'string') {
                                // Single error string
                                errorMessage += `<li>${errors}</li>`;
                            }
                        });

                        errorMessage += '</ul>';
                    } else if (typeof errorData === 'string') {
                        // Single error string
                        errorMessage = errorData;
                    }

                    showToast(errorMessage, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Reset submission status and re-enable the button
                isSubmitting = false;
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;

                showToast('Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.', 'error');
            });
        });

        // Add event handler for the back button
        document.getElementById('backButton').addEventListener('click', function(e) {
            if (formHasChanges() && !confirm('Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?')) {
                e.preventDefault();
            } else {
                isNavigatingAway = true;
            }
        });
    });
</script>
@endpush
