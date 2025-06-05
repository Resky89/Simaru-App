@extends('Layout.app')

@section('title', 'Formulir Permintaan')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Request Form Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center">
                            <a href="{{ route('procurement.request') }}" id="backButton"
                                class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORMULIR PERMINTAAN</h1>
                        </div>
                    </div>

                    <!-- Form -->
                    @if((request()->has('id') && hasPermission('procurement:request:edit')) || (!request()->has('id') && hasPermission('procurement:request:create')))
                        <form id="requestForm" class="w-full space-y-6" data-no-loading>
                            @csrf
                            <input type="hidden" id="procurement_id" name="procurement_id">

                            <!-- Title -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Judul Permintaan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="title" name="title"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Judul Permintaan">
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Judul permintaan harus diisi</div>
                            </div>

                            <!-- Priority  -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Prioritas <span
                                        class="text-red-500">*</span></label>
                                <select id="priority" name="priority"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    <option value="" disabled selected>Pilih prioritas</option>
                                    <option value="High">Tinggi</option>
                                    <option value="Medium">Sedang</option>
                                    <option value="Low">Rendah</option>
                                </select>
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Prioritas harus dipilih</div>
                            </div>

                            <!-- Justification -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Justifikasi <span
                                        class="text-red-500">*</span></label>
                                <textarea id="justification" name="justification"
                                    class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Justifikasi" rows="3"></textarea>
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Justifikasi harus diisi (minimal 10
                                    karakter)</div>
                            </div>

                            <!-- Item List -->
                            <div class="space-y-4">
                                <label class="block text-base font-semibold text-[#666666]">Daftar Aset</label>

                                <div id="itemContainer" class="space-y-6">
                                    <div class="item-entry p-6 border border-[#CCCCCC] rounded-lg relative">
                                        <!-- Delete Button (On Border) - Initially hidden for first item -->
                                        <button type="button"
                                            class="remove-item-btn absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200 shadow-md z-10 hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <!-- Asset Selection Type -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-medium text-[#666666]">Tipe Aset <span
                                                        class="text-red-500">*</span></label>
                                                <select
                                                    class="asset-type-selector w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                    <option value="new">Aset Baru</option>
                                                    <option value="existing">Aset yang Ada</option>
                                                </select>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus
                                                    dipilih</div>
                                            </div>

                                            <!-- Item Name (for new assets) -->
                                            <div class="space-y-2 asset-name-container">
                                                <label class="block text-base font-medium text-[#666666]">Nama Aset <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="details[0][asset_name]"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                                                    placeholder="Nama Aset">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus
                                                    diisi</div>
                                            </div>

                                            <!-- Asset Master Selection (for existing assets) - initially hidden -->
                                            <div class="space-y-2 asset-master-container hidden">
                                                <label class="block text-base font-medium text-[#666666]">Pilih Aset yang Ada
                                                    <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text"
                                                        class="asset-master-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                        placeholder="Cari aset..." autocomplete="off">
                                                    <input type="hidden" name="details[0][asset_master_id]"
                                                        class="asset-master-id">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Aset harus
                                                        dipilih</div>

                                                    <!-- Dropdown -->
                                                    <div
                                                        class="asset-master-dropdown absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div class="asset-master-loading p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat daftar aset...</span>
                                                        </div>
                                                        <ul class="asset-master-list py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Quantity -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-medium text-[#666666]">Jumlah <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number" name="details[0][quantity]"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                                                    placeholder="Jumlah" min="1">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Jumlah harus diisi
                                                </div>
                                            </div>

                                            <!-- Unit Price -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-medium text-[#666666]">Harga Satuan <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number" name="details[0][estimated_unit_price]"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                                    placeholder="Harga Satuan" min="0">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Harga satuan harus
                                                    diisi</div>
                                            </div>
                                        </div>

                                        <!-- Specifications -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-medium text-[#666666]">Spesifikasi</label>
                                            <textarea name="details[0][specifications]"
                                                class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                                                placeholder="Spesifikasi" rows="2"></textarea>
                                        </div>

                                        <!-- Notes -->
                                        <div class="space-y-2 mt-4">
                                            <label class="block text-base font-medium text-[#666666]">Catatan</label>
                                            <textarea name="details[0][notes]"
                                                class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 notes"
                                                placeholder="Catatan (opsional)" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Item Button -->
                                <div class="flex justify-end">
                                    <button type="button" id="addItemBtn"
                                        class="w-12 h-12 rounded-full bg-[#213268] text-white flex items-center justify-center shadow-lg hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Form Buttons -->
                            <div class="flex gap-4 mt-8">
                                <button type="submit" id="submitButton"
                                    class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    KIRIM
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- No permission message -->
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                            <p>Maaf, Anda tidak memiliki izin untuk {{ request()->has('id') ? 'mengedit' : 'membuat' }}
                                permintaan pengadaan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemContainer = document.getElementById('itemContainer');
                const requestForm = document.getElementById('requestForm');

                // Function to format date in Indonesian
                function formatDateIndonesian(dateString) {
                    if (!dateString) return '';

                    try {
                        // Parse the date string
                        const date = new Date(dateString);
                        if (isNaN(date)) return dateString;

                        // Indonesian month names
                        const months = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];

                        const day = date.getDate();
                        const month = months[date.getMonth()];
                        const year = date.getFullYear();
                        const hours = date.getHours().toString().padStart(2, '0');

                        return `${day} ${month} ${year}`;
                    } catch (e) {
                        console.error('Date formatting error:', e);
                        return dateString;
                    }
                }

                // Add a flag to track if we're currently submitting/redirecting to prevent unwanted navigation
                let isNavigatingAway = false;

                // Improve navigation handling with SweetAlert for internal links
                document.addEventListener('click', function (e) {
                    // Skip if we're already navigating away or submitting
                    if (isSubmitting || isNavigatingAway) {
                        return;
                    }

                    // Find closest anchor tag if the click was on a child element
                    const anchor = e.target.closest('a');
                    if (!anchor) return; // Not clicking on a link

                    // Skip links without href or with href="#" or javascript:void(0)
                    if (!anchor.href ||
                        anchor.href === window.location.href ||
                        anchor.href === window.location.href + '#' ||
                        anchor.href.startsWith('javascript:')) {
                        return;
                    }

                    // Skip links with specific data attributes (e.g., download links, modals)
                    if (anchor.hasAttribute('data-skip-confirm') ||
                        anchor.hasAttribute('download') ||
                        anchor.target === '_blank') {
                        return;
                    }

                    // Skip if the form has no changes
                    if (!formHasChanges()) {
                        return;
                    }

                    // Prevent the default navigation
                    e.preventDefault();

                    // Show SweetAlert confirmation
                    showSweetAlert('Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?', 'warning', {
                        title: 'Perubahan Belum Disimpan',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tinggalkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#213268',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed leaving, set flag and navigate
                            isNavigatingAway = true;
                            window.location.href = anchor.href;
                        }
                        // If not confirmed, do nothing - user stays on page
                    });
                });

                window.addEventListener('beforeunload', function (e) {
                    // Only show if there are form changes and we're not already submitting or redirecting
                    if (!isSubmitting && !isNavigatingAway && formHasChanges()) {
                        // Modern browsers will show a generic message regardless of what we set here
                        e.preventDefault();
                        e.returnValue = '';
                        return '';
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

                // Show SweetAlert notifications for session messages on page load
                @if(session('success'))
                    showSweetAlert("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showSweetAlert("{{ session('error') }}", 'error');
                @endif

                // Array to store asset master data
                let assetMasters = [];

                // Track selected asset master IDs
                let selectedAssetMasterIds = new Set();

                // Preload asset masters when page loads
                fetchAssetMasters().then(data => {
                    console.log('Asset masters preloaded successfully:', data.length, 'items');
                }).catch(error => {
                    console.error('Error preloading asset masters:', error);
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

                    // Use the same API endpoint as in UnitAsset.blade.php
                    return fetch('{{ route("asset-master") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Match the data structure from UnitAsset.blade.php
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
                            console.error('Error loading asset masters:', error);

                            // Hide all loading indicators on error
                            document.querySelectorAll('.asset-master-loading').forEach(loading => {
                                loading.style.display = 'none';
                            });

                            // Show error in the dropdown
                            document.querySelectorAll('.asset-master-list').forEach(list => {
                                const errorItem = document.createElement('li');
                                errorItem.className = 'px-4 py-2 text-sm text-red-500';
                                errorItem.textContent = 'Gagal memuat daftar aset';
                                list.appendChild(errorItem);
                            });

                            // Also show SweetAlert for the error
                            showSweetAlert('Gagal memuat daftar aset master. Silakan coba lagi.', 'error');

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
                        if (availableAssets.length === 0) {
                            const noResults = document.createElement('li');
                            noResults.className = 'px-4 py-2 text-sm text-gray-500 italic no-results-item';
                            noResults.textContent = 'Tidak ada aset tersedia';
                            listElement.appendChild(noResults);
                        } else {
                            availableAssets.forEach(asset => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                li.textContent = asset.asset_name || 'Aset tidak dikenal';
                                li.setAttribute('data-id', asset.asset_master_id);
                                li.setAttribute('data-name', asset.asset_name || 'Aset tidak dikenal');

                                // Add click handler
                                li.addEventListener('click', function () {
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
                        searchInput.addEventListener('input', function () {
                            if (!this.value.trim()) {
                                hiddenInput.value = '';
                                // Update available options for all dropdowns
                                updateSelectedAssetMasterIds();
                                updateAssetMasterDropdowns();
                            }
                        });

                        // Show dropdown on focus
                        searchInput.addEventListener('focus', function () {
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
                        searchInput.addEventListener('input', function () {
                            const value = this.value.toLowerCase();

                            // Show dropdown if it's hidden and we're typing
                            if (dropdown.classList.contains('hidden') && value.trim() !== '') {
                                dropdown.classList.remove('hidden');
                            }

                            const items = list.querySelectorAll('li:not(.no-results-item)');

                            let hasVisibleItems = false;

                            // Remove any previous "no results" item
                            list.querySelectorAll('.no-results-item').forEach(el => el.remove());

                            items.forEach(item => {
                                const text = item.textContent.toLowerCase();
                                if (text.includes(value)) {
                                    item.style.display = '';
                                    hasVisibleItems = true;
                                } else {
                                    item.style.display = 'none';
                                }
                            });

                            // Show "No results" message if needed
                            if (!hasVisibleItems) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-sm text-gray-500 italic no-results-item';
                                noResults.textContent = 'Tidak ada aset yang cocok';
                                list.appendChild(noResults);
                            }
                        });

                        // Hide dropdown when clicking outside
                        document.addEventListener('click', function (e) {
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
                } else {
                    // Initialize with at least one item entry in create mode
                    if (itemContainer.querySelectorAll('.item-entry').length === 0) {
                        addItemEntry(0);
                    }
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
                            selector.addEventListener('change', function () {
                                const itemEntry = this.closest('.item-entry');
                                handleAssetTypeChange(this, itemEntry);
                            });
                        }
                    });
                }

                // Function to load existing procurement data
                function loadProcurementData(id) {
                    // Show loading indicator
                    const submitButton = document.getElementById('submitButton');
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        MENYIMPAN...
                    `;

                    fetch(`/procurement/request/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Gagal mengambil data permintaan (${response.status})`);
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
                                if (procurement.details && procurement.details.length > 0) {
                                    procurement.details.forEach((detail, index) => {
                                        addItemEntry(index, detail);
                                    });
                                } else {
                                    // If no details, add at least one empty item row
                                    addItemEntry(0);
                                }

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
                                showSweetAlert('Gagal memuat data permintaan', 'error');
                            }

                            // Restore button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'KIRIM';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showSweetAlert('Gagal memuat data permintaan: ' + error.message, 'error', {
                                title: 'Gagal Memuat Data',
                                footer: 'Silakan coba muat ulang halaman'
                            });

                            // Restore button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'KIRIM';
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
                                <label class="block text-base font-medium text-[#666666]">Tipe Aset <span class="text-red-500">*</span></label>
                                <select class="asset-type-selector w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    <option value="new">Aset Baru</option>
                                    <option value="existing">Aset yang Ada</option>
                                </select>
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus dipilih</div>
                            </div>

                            <!-- Item Name (for new assets) -->
                            <div class="space-y-2 asset-name-container">
                                <label class="block text-base font-medium text-[#666666]">Nama Aset <span class="text-red-500">*</span></label>
                                <input type="text" name="details[${index}][asset_name]"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                                    placeholder="Nama Aset" value="${data && data.asset_name ? data.asset_name : ''}">
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus diisi</div>
                            </div>

                            <!-- Asset Master Selection (for existing assets) - initially hidden -->
                            <div class="space-y-2 asset-master-container hidden">
                                <label class="block text-base font-medium text-[#666666]">Pilih Aset yang Ada <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" class="asset-master-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Cari aset..." autocomplete="off">
                                    <input type="hidden" name="details[${index}][asset_master_id]" class="asset-master-id">
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Aset harus dipilih</div>

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
                                <label class="block text-base font-medium text-[#666666]">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" name="details[${index}][quantity]"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                                    placeholder="Jumlah" min="1" value="${data ? data.quantity : ''}">
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Jumlah harus diisi</div>
                            </div>

                            <!-- Unit Price -->
                            <div class="space-y-2">
                                <label class="block text-base font-medium text-[#666666]">Harga Satuan <span class="text-red-500">*</span></label>
                                <input type="number" name="details[${index}][estimated_unit_price]"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                    placeholder="Harga Satuan" min="0" value="${data ? data.estimated_unit_price : ''}">
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Harga satuan harus diisi</div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div class="space-y-2">
                            <label class="block text-base font-medium text-[#666666]">Spesifikasi</label>
                            <textarea name="details[${index}][specifications]"
                                class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                                placeholder="Spesifikasi" rows="2">${data ? data.specifications || '' : ''}</textarea>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 mt-4">
                            <label class="block text-base font-medium text-[#666666]">Catatan</label>
                            <textarea name="details[${index}][notes]"
                                class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 notes"
                                placeholder="Catatan (opsional)" rows="2">${data ? data.notes || '' : ''}</textarea>
                        </div>
                    `;

                    itemContainer.appendChild(newItem);

                    // Add event listener to the new remove button
                    const removeBtn = newItem.querySelector('.remove-item-btn');
                    removeBtn.addEventListener('click', function () {
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
                    assetTypeSelector.addEventListener('change', function () {
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
                addItemBtn.addEventListener('click', function () {
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

                // Function to show SweetAlert notifications
                function showSweetAlert(message, type = 'success', options = {}) {
                    const iconMap = {
                        success: 'success',
                        error: 'error',
                        warning: 'warning',
                        info: 'info',
                        question: 'question'
                    };

                    // Default options
                    const defaultOptions = {
                        title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi',
                        html: message,
                        icon: iconMap[type] || 'info',
                        confirmButtonText: options.confirmButtonText || 'OK',
                        confirmButtonColor: options.confirmButtonColor || '#213268',
                        customClass: {
                            popup: 'swal-custom-popup',
                            title: 'swal-custom-title',
                            htmlContainer: 'swal-custom-content',
                            confirmButton: 'swal-custom-confirm',
                            cancelButton: 'swal-custom-cancel'
                        },
                        buttonsStyling: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        }
                    };

                    // Merge with custom options
                    const mergedOptions = { ...defaultOptions, ...options };

                    // Add specific options based on alert type
                    if (type === 'success' && !options.timer === undefined) {
                        // Auto close success messages after 2.5 seconds
                        mergedOptions.timer = 2500;
                        mergedOptions.timerProgressBar = true;
                    } else if (type === 'error' && !options.showCloseButton) {
                        // Make error alerts more prominent
                        mergedOptions.confirmButtonColor = '#d33';
                        mergedOptions.showCloseButton = true;
                    }

                    // Add custom styles for SweetAlert
                    if (!document.getElementById('swal-custom-styles')) {
                        const styleTag = document.createElement('style');
                        styleTag.id = 'swal-custom-styles';
                        styleTag.innerHTML = `
                            /* SweetAlert Custom Styles */
                            .swal2-popup {
                                border-radius: 15px;
                                padding: 1.5rem;
                                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                            }
                            .swal-custom-title {
                                font-weight: 600;
                                font-size: 1.5rem;
                                color: #333;
                            }
                            .swal-custom-content {
                                font-size: 1rem;
                                color: #555;
                                margin-top: 0.5rem;
                            }
                            .swal-custom-content ul {
                                text-align: left;
                                margin-top: 1rem;
                                margin-bottom: 1rem;
                            }
                            .swal-custom-confirm {
                                padding: 0.5rem 1.5rem;
                                font-weight: 500;
                            }
                            .swal-custom-cancel {
                                padding: 0.5rem 1.5rem;
                                font-weight: 500;
                            }
                            .swal2-timer-progress-bar {
                                background: rgba(33, 50, 104, 0.5);
                            }
                            .swal2-icon {
                                margin: 1rem auto;
                            }
                        `;
                        document.head.appendChild(styleTag);
                    }

                    // Add animate.css CDN for animations if not already loaded
                    if (!document.getElementById('animate-css')) {
                        const animateLink = document.createElement('link');
                        animateLink.id = 'animate-css';
                        animateLink.rel = 'stylesheet';
                        animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
                        document.head.appendChild(animateLink);
                    }

                    // Fire the alert and return the Promise for chaining
                    return Swal.fire(mergedOptions);
                }

                // Function to validate a form field
                function validateField(field) {
                    if (!field) return false;

                    const fieldContainer = field.closest('.space-y-2');
                    const errorMessage = fieldContainer ? fieldContainer.querySelector('.error-message') : null;

                    if (field.tagName.toLowerCase() === 'select') {
                        if (!field.value) {
                            field.classList.add('border-red-500');
                            if (errorMessage) errorMessage.classList.remove('hidden');
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            return true;
                        }
                    } else if (field.tagName.toLowerCase() === 'textarea' && field.id === 'justification') {
                        // Special validation for justification - minimum 10 characters
                        if (!field.value.trim() || field.value.trim().length < 10) {
                            field.classList.add('border-red-500');
                            if (errorMessage) {
                                errorMessage.textContent = field.value.trim() ? 'Justifikasi minimal 10 karakter' : 'Justifikasi harus diisi';
                                errorMessage.classList.remove('hidden');
                            }
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            return true;
                        }
                    } else if (field.classList.contains('quantity')) {
                        // Specific validation for quantity fields
                        const value = parseInt(field.value, 10);
                        if (isNaN(value) || value <= 0) {
                            field.classList.add('border-red-500');
                            if (errorMessage) {
                                errorMessage.textContent = 'Jumlah harus lebih dari 0';
                                errorMessage.classList.remove('hidden');
                            }
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            return true;
                        }
                    } else if (field.classList.contains('unit-price')) {
                        // Specific validation for price fields
                        const value = parseFloat(field.value);
                        if (isNaN(value) || value < 0) {
                            field.classList.add('border-red-500');
                            if (errorMessage) {
                                errorMessage.textContent = 'Harga tidak boleh negatif';
                                errorMessage.classList.remove('hidden');
                            }
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            return true;
                        }
                    } else {
                        if (!field.value.trim()) {
                            field.classList.add('border-red-500');
                            if (errorMessage) errorMessage.classList.remove('hidden');
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            return true;
                        }
                    }
                }

                // Add input/change event listeners to clear error styling on fields
                document.getElementById('title').addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('priority').addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('justification').addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                // CSS for validation styling
                document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        /* Field validation */
                        .border-red-500 {
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

                // Handle form submission
                let isSubmitting = false; // Flag to track submission status
                requestForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Validate form before submitting
                    let isValid = true;

                    // Validate main form fields
                    if (!validateField(document.getElementById('title'))) isValid = false;
                    if (!validateField(document.getElementById('priority'))) isValid = false;
                    if (!validateField(document.getElementById('justification'))) isValid = false;

                    // Validate item entries
                    itemContainer.querySelectorAll('.item-entry').forEach((item, index) => {
                        const assetTypeSelector = item.querySelector('.asset-type-selector');
                        if (!validateField(assetTypeSelector)) isValid = false;

                        // Validate based on asset type
                        if (assetTypeSelector.value === 'new') {
                            if (!validateField(item.querySelector('.asset-name'))) isValid = false;
                        } else {
                            // For existing assets, validate asset_master_id
                            const assetMasterId = item.querySelector('.asset-master-id');
                            // Use the search field for UI validation but check the hidden input value
                            const assetMasterSearch = item.querySelector('.asset-master-search');

                            if (!assetMasterId.value) {
                                assetMasterSearch.classList.add('border-red-500');
                                const errorElement = assetMasterSearch.closest('.relative').querySelector('.error-message');
                                if (errorElement) errorElement.classList.remove('hidden');
                                isValid = false;
                            }
                        }

                        // Validate quantity and price
                        if (!validateField(item.querySelector('.quantity'))) isValid = false;
                        if (!validateField(item.querySelector('.unit-price'))) isValid = false;
                    });

                    // If validation fails, don't proceed
                    if (!isValid) {
                        showSweetAlert('Silakan perbaiki semua kesalahan dalam formulir.', 'error');
                        return;
                    }

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
                    const data = {};

                    // Simple fields
                    data.title = formData.get('title');
                    data.priority = formData.get('priority');
                    data.justification = formData.get('justification');

                    // Process all item entries to create proper details array
                    // Initialize details as an array, not an object property
                    data.details = [];

                    // Get all item entries from the container
                    itemContainer.querySelectorAll('.item-entry').forEach((item, index) => {
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
                                // Show success message with automatic redirect
                                showSweetAlert(
                                    isUpdate ? 'Pengadaan berhasil diperbarui' : 'Pengadaan berhasil dibuat',
                                    'success',
                                    {
                                        timer: 1500,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            // Set flag to indicate we're navigating away intentionally
                                            isNavigatingAway = true;
                                        },
                                        willClose: () => {
                                            // Redirect after toast closes
                                            window.location.href = '{{ route("procurement.request") }}';
                                        }
                                    }
                                );
                            } else {
                                // Reset submission status and re-enable the button
                                isSubmitting = false;
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;

                                const errorData = result.errors || {};

                                // Initialize error message
                                let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda:';
                                let errorList = [];

                                // Handle array-formatted errors
                                if (Array.isArray(errorData)) {
                                    errorData.forEach(error => {
                                        if (error.path && error.message) {
                                            errorList.push(`${error.message}`);

                                            // Highlight field with error
                                            if (error.path === 'title') {
                                                highlightFieldError('title', error.message);
                                            } else if (error.path === 'priority') {
                                                highlightFieldError('priority', error.message);
                                            } else if (error.path === 'justification') {
                                                highlightFieldError('justification', error.message);
                                            } else if (error.path.startsWith('details')) {
                                                // Handle details array errors
                                                highlightDetailsFieldError(error.path, error.message);
                                            }
                                        } else if (typeof error === 'string') {
                                            errorList.push(error);
                                        }
                                    });
                                }
                                // Handle object-formatted errors (backward compatibility)
                                else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                    // Process each error field and highlight form fields
                                    Object.entries(errorData).forEach(([field, errors]) => {
                                        // Highlight field with error
                                        if (field === 'title' || field === 'priority' || field === 'justification') {
                                            highlightFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                        }

                                        // Handle detail fields with array notation (e.g., details.0.asset_name)
                                        if (field.includes('details.')) {
                                            highlightDetailsFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                        }

                                        if (Array.isArray(errors)) {
                                            // Multiple errors for this field
                                            errors.forEach(err => {
                                                errorList.push(`${err}`);
                                            });
                                        } else if (typeof errors === 'string') {
                                            // Single error string
                                            errorList.push(`${errors}`);
                                        }
                                    });
                                } else if (typeof errorData === 'string') {
                                    // Single error string
                                    errorMessage = errorData;
                                }

                                // Format error message with list if we have specific errors
                                if (errorList.length > 0) {
                                    errorMessage += '<ul class="mt-2 list-disc pl-5">';
                                    errorList.forEach(err => {
                                        errorMessage += `<li>${err}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                }

                                showSweetAlert(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            // Reset submission status and re-enable the button
                            isSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;

                            showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.', 'error');
                        });
                });

                // Helper function to highlight field errors
                function highlightFieldError(fieldName, errorMessage) {
                    const field = document.getElementById(fieldName);
                    if (!field) return;

                    field.classList.add('border-red-500');

                    const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = errorMessage;
                        errorElement.classList.remove('hidden');
                    }
                }

                // Helper function to highlight errors in the details array
                function highlightDetailsFieldError(fieldPath, errorMessage) {
                    // Parse the path to get index and field name (e.g., details.0.asset_name)
                    const parts = fieldPath.split('.');
                    if (parts.length >= 3) {
                        const index = parseInt(parts[1]);
                        const subField = parts[2];

                        // Find and highlight the field in the specific item entry
                        const items = itemContainer.querySelectorAll('.item-entry');
                        if (items[index]) {
                            let field;
                            let errorElement;

                            switch (subField) {
                                case 'asset_name':
                                    field = items[index].querySelector('.asset-name');
                                    break;
                                case 'asset_master_id':
                                    field = items[index].querySelector('.asset-master-search');
                                    break;
                                case 'quantity':
                                    field = items[index].querySelector('.quantity');
                                    break;
                                case 'estimated_unit_price':
                                    field = items[index].querySelector('.unit-price');
                                    break;
                                case 'specifications':
                                    field = items[index].querySelector('.specifications');
                                    break;
                                case 'notes':
                                    field = items[index].querySelector('.notes');
                                    break;
                            }

                            if (field) {
                                field.classList.add('border-red-500');
                                errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                                if (errorElement) {
                                    errorElement.textContent = errorMessage;
                                    errorElement.classList.remove('hidden');
                                }
                            }
                        }
                    } else if (fieldPath === 'details') {
                        // Generic error for the whole details section
                        showSweetAlert('Error: ' + errorMessage, 'error');
                    }
                }

                // Add event handler for the back button - keep this specific handling
                document.getElementById('backButton').addEventListener('click', function (e) {
                    if (formHasChanges()) {
                        e.preventDefault();
                        showSweetAlert('Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?', 'warning', {
                            title: 'Perubahan Belum Disimpan',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Tinggalkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#213268',
                            cancelButtonColor: '#d33'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                isNavigatingAway = true;
                                window.location.href = '{{ route("procurement.request") }}';
                            }
                        });
                    } else {
                        isNavigatingAway = true;
                    }
                });
            });
        </script>
    @endpush
@endsection
