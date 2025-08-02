@extends('Layout.app')

@section('title', 'Formulir Permintaan')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="p-4 md:p-7 bg-base-100 rounded-lg">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div class="flex items-center">
                <button type="button" id="backButton"
                    class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORMULIR PERMINTAAN</h1>
            </div>
        </div>

        <!-- Form -->
        @if((request()->has('id') && hasPermission('procurement:edit')) || (!request()->has('id') && hasPermission('procurement:create')))
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
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
                                        <input type="hidden" name="details[0][asset_master_id]" class="asset-master-id">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Aset harus
                                            dipilih</div>

                                        <!-- Dropdown -->
                                        <div
                                            class="asset-master-dropdown absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                            <div class="asset-master-loading p-2 text-gray-500 text-center">
                                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                <span>Memuat daftar aset...</span>
                                            </div>
                                            <ul class="asset-master-list py-1"></ul>
                                            <div class="asset-master-load-more p-2 text-gray-500 text-center hidden">
                                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                <span>Memuat lebih banyak aset...</span>
                                            </div>
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
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="text" name="details[0][estimated_unit_price]"
                                            class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                            placeholder="Harga Satuan" min="0" onkeyup="formatCurrency(this)"
                                            onblur="formatCurrency(this, 'blur')">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Harga satuan harus
                                            diisi</div>
                                    </div>
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
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemContainer = document.getElementById('itemContainer');
                const requestForm = document.getElementById('requestForm');

                function formatDateIndonesian(dateString) {
                    if (!dateString) return '';

                    try {
                        const date = new Date(dateString);
                        if (isNaN(date)) return dateString;

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

                let isNavigatingAway = false;

                document.addEventListener('click', function (e) {
                    if (isSubmitting || isNavigatingAway) {
                        return;
                    }

                    const anchor = e.target.closest('a');
                    if (!anchor) return;

                    if (!anchor.href ||
                        anchor.href === window.location.href ||
                        anchor.href === window.location.href + '#' ||
                        anchor.href.startsWith('javascript:')) {
                        return;
                    }

                    if (anchor.hasAttribute('data-skip-confirm') ||
                        anchor.hasAttribute('download') ||
                        anchor.target === '_blank') {
                        return;
                    }

                    if (!formHasChanges()) {
                        return;
                    }

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
                            window.location.href = anchor.href;
                        }
                    });
                });

                window.addEventListener('beforeunload', function (e) {
                    if (!isSubmitting && !isNavigatingAway && formHasChanges()) {
                        e.preventDefault();
                        e.returnValue = '';
                        return '';
                    }
                });

                function formHasChanges() {
                    if (document.getElementById('title').value ||
                        document.getElementById('priority').value ||
                        document.getElementById('justification').value) {
                        return true;
                    }

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

                @if(session('success'))
                    showSweetAlert("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showSweetAlert("{{ session('error') }}", 'error');
                @endif

                let selectedAssetMasterIds = new Set();

                addAssetTypeSelectorListeners();

                function updateSelectedAssetMasterIds() {
                    selectedAssetMasterIds.clear();
                    document.querySelectorAll('.asset-master-id').forEach(input => {
                        if (input.value) {
                            selectedAssetMasterIds.add(parseInt(input.value));
                        }
                    });
                }

                let currentPage = 1;
                let isLoading = false;
                let hasMore = true;
                let currentSearch = '';

                async function fetchAssetMasters(searchTerm = '', page = 1) {
                    const loadingElements = document.querySelectorAll('.asset-master-loading');
                    loadingElements.forEach(loading => {
                        loading.style.display = 'block';
                    });

                    let url = '{{ route("asset-master") }}';
                    const params = new URLSearchParams({
                        search: searchTerm,
                        page: page,
                        limit: 20
                    });
                    url += `?${params.toString()}`;

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        const data = await response.json();
                        const results = data.masterAssets || [];

                        loadingElements.forEach(loading => {
                            loading.style.display = 'none';
                        });
                        return { results, hasMore: results.length === 20 };
                    } catch (error) {
                        console.error('Error loading asset masters:', error);
                        loadingElements.forEach(loading => {
                            loading.style.display = 'none';
                        });
                        showSweetAlert('Gagal memuat daftar aset master. Silakan coba lagi.', 'error');
                        return { results: [], hasMore: false };
                    }
                }

                function setupAssetMasterSearch(container) {
                    const searchInput = container.querySelector('.asset-master-search');
                    const dropdown = container.querySelector('.asset-master-dropdown');
                    const list = container.querySelector('.asset-master-list');
                    const initialLoading = container.querySelector('.asset-master-loading');
                    const loadMoreLoading = container.querySelector('.asset-master-load-more');
                    const hiddenInput = container.querySelector('.asset-master-id');

                    // Essential elements check - only abort if critical elements are missing
                    if (!searchInput || !dropdown || !list || !hiddenInput) {
                        return;
                    }

                    let page = 1;
                    let hasMore = true;
                    let isLoading = false;
                    let searchTerm = '';

                    // Check if this is edit mode (has existing value)
                    const isEditMode = hiddenInput.value && hiddenInput.value !== '';

                    async function loadAssetMasters(newSearchTerm, reset = true) {
                        if (isLoading) {
                            return;
                        }

                        isLoading = true;

                        const currentPage = reset ? 1 : page;

                        if (reset) {
                            searchTerm = newSearchTerm;
                            list.innerHTML = '';
                            if (initialLoading) initialLoading.style.display = 'block';
                            if (loadMoreLoading) loadMoreLoading.style.display = 'none';
                        } else {
                            if (initialLoading) initialLoading.style.display = 'none';
                            if (loadMoreLoading) loadMoreLoading.style.display = 'block';
                        }

                        try {
                            const { results, hasMore: newHasMore } = await fetchAssetMasters(searchTerm, currentPage);

                            updateSelectedAssetMasterIds();
                            const currentAssetMasterId = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                            const availableAssets = results.filter(asset => {
                                const assetId = parseInt(asset.asset_master_id);
                                return !selectedAssetMasterIds.has(assetId) || (currentAssetMasterId === assetId);
                            });

                            availableAssets.forEach(asset => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                li.textContent = asset.asset_name || 'Aset tidak dikenal';
                                li.setAttribute('data-id', asset.asset_master_id);
                                li.setAttribute('data-name', asset.asset_name || 'Aset tidak dikenal');

                                li.addEventListener('click', function () {
                                    const clickedId = parseInt(this.getAttribute('data-id'));
                                    updateSelectedAssetMasterIds();
                                    const current = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                                    if (selectedAssetMasterIds.has(clickedId) && clickedId !== current) {
                                        showSweetAlert('Aset ini sudah dipilih di item lain.', 'warning');
                                        return;
                                    }

                                    searchInput.value = this.getAttribute('data-name');
                                    hiddenInput.value = this.getAttribute('data-id');
                                    dropdown.classList.add('hidden');

                                    updateSelectedAssetMasterIds();
                                    document.querySelectorAll('.asset-master-list li[data-id="' + clickedId + '"]').forEach(otherLi => {
                                        const otherContainer = otherLi.closest('.asset-master-container');
                                        if (otherContainer && otherContainer !== container) {
                                            const otherHidden = otherContainer.querySelector('.asset-master-id');
                                            if (otherHidden && otherHidden.value != clickedId) {
                                                otherLi.remove();
                                            }
                                        }
                                    });
                                });

                                list.appendChild(li);
                            });

                            hasMore = newHasMore;
                            if (hasMore) page = currentPage + 1;

                            if (availableAssets.length === 0 && list.children.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-sm text-gray-500 italic';
                                noResults.textContent = 'Tidak ada aset tersedia';
                                list.appendChild(noResults);
                            }
                        } catch (error) {
                            console.error('Error loading asset masters:', error);
                        } finally {
                            if (initialLoading) initialLoading.style.display = 'none';
                            if (loadMoreLoading) loadMoreLoading.style.display = 'none';
                            isLoading = false;
                        }
                    }

                    searchInput.addEventListener('input', function () {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            loadAssetMasters(this.value, true);
                        }, 300);
                    });

                    searchInput.addEventListener('focus', async function () {
                        dropdown.classList.remove('hidden');
                        if (list.innerHTML === '') {
                            await loadAssetMasters('', true);
                        }
                    });

                    // Add scroll listener for load more
                    dropdown.addEventListener('scroll', () => {
                        if (dropdown.scrollTop + dropdown.clientHeight >= dropdown.scrollHeight - 10 && hasMore && !isLoading) {
                            loadAssetMasters(searchTerm, false);
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!container.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                const urlParams = new URLSearchParams(window.location.search);
                const procurementId = urlParams.get('id');

                if (procurementId) {
                    document.getElementById('procurement_id').value = procurementId;

                    loadProcurementData(procurementId);
                } else {
                    if (itemContainer.querySelectorAll('.item-entry').length === 0) {
                        addItemEntry(0);
                    }
                }

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

                        assetMasterInput.value = '';
                        const assetMasterSearch = assetMasterContainer.querySelector('.asset-master-search');
                        if (assetMasterSearch) {
                            assetMasterSearch.value = '';
                        }

                        updateSelectedAssetMasterIds();
                    } else {
                        assetNameContainer.classList.add('hidden');
                        assetMasterContainer.classList.remove('hidden');
                        assetNameInput.disabled = true;
                        assetMasterInput.disabled = false;
                        assetNameInput.required = false;
                        assetMasterInput.required = true;

                        setupAssetMasterSearch(assetMasterContainer);
                    }
                }

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

                function loadProcurementData(id) {
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

                                document.getElementById('title').value = procurement.title;
                                document.getElementById('priority').value = procurement.priority;
                                document.getElementById('justification').value = procurement.justification;

                                itemContainer.innerHTML = '';

                                if (procurement.details && procurement.details.length > 0) {
                                    procurement.details.forEach((detail, index) => {
                                        addItemEntry(index, detail);
                                    });
                                } else {
                                    addItemEntry(0);
                                }

                                // Wait for all items to be added before setting up asset master search
                                setTimeout(() => {
                                    document.querySelectorAll('.item-entry').forEach((entry, index) => {
                                        const detail = procurement.details[index];
                                        if (detail) {
                                            const unitPriceInput = entry.querySelector('.unit-price');
                                            if (unitPriceInput) {
                                                let unitPrice = detail.estimated_unit_price || '';
                                                if (unitPrice !== '') {
                                                    unitPrice = parseFloat(unitPrice).toLocaleString('id-ID').replace(/,/g, '.');

                                                    if (!unitPrice.includes(',')) {
                                                        unitPrice += ',00';
                                                    }

                                                    unitPriceInput.value = unitPrice;
                                                }
                                            }

                                            // Setup asset type and dropdown for edit mode
                                            const selector = entry.querySelector('.asset-type-selector');
                                            const assetMasterContainer = entry.querySelector('.asset-master-container');

                                            if (detail.asset_master_id) {
                                                selector.value = 'existing';

                                                // Show asset master container
                                                const assetNameContainer = entry.querySelector('.asset-name-container');
                                                assetNameContainer.classList.add('hidden');
                                                assetMasterContainer.classList.remove('hidden');

                                                // Set up the search input and hidden field
                                                const assetMasterSearch = assetMasterContainer.querySelector('.asset-master-search');
                                                const assetMasterId = assetMasterContainer.querySelector('.asset-master-id');

                                                if (assetMasterSearch && assetMasterId) {
                                                    assetMasterId.value = detail.asset_master_id;
                                                    assetMasterSearch.value = detail.asset_name || 'Aset #' + detail.asset_master_id;

                                                    // Re-setup asset master search for this container
                                                    setupAssetMasterSearch(assetMasterContainer);
                                                }
                                            } else {
                                                selector.value = 'new';
                                                // Keep asset name container visible, hide master container
                                                const assetNameContainer = entry.querySelector('.asset-name-container');
                                                assetNameContainer.classList.remove('hidden');
                                                assetMasterContainer.classList.add('hidden');
                                            }
                                        }
                                    });

                                    updateDeleteButtons();
                                    addAssetTypeSelectorListeners();
                                    updateSelectedAssetMasterIds();
                                    initializeDeleteButtons();
                                }, 100);

                            } else {
                                showSweetAlert('Gagal memuat data permintaan', 'error');
                            }

                            submitButton.disabled = false;
                            submitButton.innerHTML = 'KIRIM';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showSweetAlert('Gagal memuat data permintaan: ' + error.message, 'error', {
                                title: 'Gagal Memuat Data',
                                footer: 'Silakan coba muat ulang halaman'
                            });

                            submitButton.disabled = false;
                            submitButton.innerHTML = 'KIRIM';
                        });
                }

                function updateDeleteButtons() {
                    const items = itemContainer.querySelectorAll('.item-entry');
                    if (items.length === 1) {
                        items[0].querySelector('.remove-item-btn').classList.add('hidden');
                    } else {
                        items.forEach(item => {
                            item.querySelector('.remove-item-btn').classList.remove('hidden');
                        });
                    }
                }

                function initializeDeleteButtons() {
                    document.querySelectorAll('.remove-item-btn').forEach(button => {
                        const newButton = button.cloneNode(true);
                        button.parentNode.replaceChild(newButton, button);
                        newButton.addEventListener('click', function (e) {
                            e.preventDefault();
                            const itemEntry = this.closest('.item-entry');
                            if (itemEntry) {
                                itemEntry.remove();
                                updateDeleteButtons();
                                updateInputNames();
                                updateSelectedAssetMasterIds();
                            }
                        });
                    });

                    updateDeleteButtons();
                }

                initializeDeleteButtons();

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
                                                                                                                        <div class="asset-master-load-more p-2 text-gray-500 text-center hidden">
                                                                                                                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                                                                            </svg>
                                                                                                                            <span>Memuat lebih banyak aset...</span>
                                                                                                                        </div>
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
                                                                                                                <div class="relative">
                                                                                                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                                                                                        <span class="text-gray-500">Rp</span>
                                                                                                                    </div>
                                                                                                                <input type="text" name="details[${index}][estimated_unit_price]"
                                                                                                                    class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                                                                                                    placeholder="Harga Satuan" min="0" value="${data ? data.estimated_unit_price : ''}" onkeyup="formatCurrency(this)" onblur="formatCurrency(this, 'blur')"
                                                                                                                    >
                                                                                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Harga satuan harus diisi</div>
                                                                                                                </div>
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

                    const removeBtn = newItem.querySelector('.remove-item-btn');
                    removeBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const itemEntry = this.closest('.item-entry');
                        if (itemEntry) {
                            itemEntry.remove();
                            updateDeleteButtons();
                            updateInputNames();
                            updateSelectedAssetMasterIds();
                        }
                    });

                    const assetTypeSelector = newItem.querySelector('.asset-type-selector');
                    assetTypeSelector.addEventListener('change', function () {
                        handleAssetTypeChange(this, newItem);
                    });

                    const assetMasterContainer = newItem.querySelector('.asset-master-container');

                    setupAssetMasterSearch(assetMasterContainer);

                    if (data && data.asset_master_id) {
                        assetTypeSelector.value = 'existing';
                        handleAssetTypeChange(assetTypeSelector, newItem);

                        const assetMasterId = newItem.querySelector('.asset-master-id');
                        if (assetMasterId) {
                            assetMasterId.value = data.asset_master_id;
                        }

                        const assetMasterSearch = newItem.querySelector('.asset-master-search');
                        if (assetMasterSearch) {
                            assetMasterSearch.value = data.asset_name || 'Aset #' + data.asset_master_id;
                        }
                    }

                    updateSelectedAssetMasterIds();
                    updateDeleteButtons();
                }

                addItemBtn.addEventListener('click', function () {
                    const itemCount = itemContainer.querySelectorAll('.item-entry').length;
                    addItemEntry(itemCount);
                    updateDeleteButtons();
                    addAssetTypeSelectorListeners();
                    initializeDeleteButtons();
                });

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

                function showSweetAlert(message, type = 'success', options = {}) {
                    const iconMap = {
                        success: 'success',
                        error: 'error',
                        warning: 'warning',
                        info: 'info',
                        question: 'question'
                    };

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

                    const mergedOptions = { ...defaultOptions, ...options };

                    if (type === 'success' && !options.timer === undefined) {
                                mergedOptions.timer = 2500;
                                mergedOptions.timerProgressBar = true;
                            } else if (type === 'error' && !options.showCloseButton) {
                                mergedOptions.confirmButtonColor = '#d33';
                                mergedOptions.showCloseButton = true;
                            }

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

                            if (!document.getElementById('animate-css')) {
                                const animateLink = document.createElement('link');
                                animateLink.id = 'animate-css';
                                animateLink.rel = 'stylesheet';
                                animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
                                document.head.appendChild(animateLink);
                            }

                            return Swal.fire(mergedOptions);
                        }

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

                        document.head.insertAdjacentHTML('beforeend', `
                                                                                    <style>
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

                        let isSubmitting = false;
                        requestForm.addEventListener('submit', function (e) {
                            e.preventDefault();

                            let isValid = true;

                            if (!validateField(document.getElementById('title'))) isValid = false;
                            if (!validateField(document.getElementById('priority'))) isValid = false;
                            if (!validateField(document.getElementById('justification'))) isValid = false;

                            // Check if there's at least one item
                            const itemEntries = itemContainer.querySelectorAll('.item-entry');
                            if (itemEntries.length === 0) {
                                showSweetAlert('Setidaknya satu item aset harus ditambahkan.', 'error');
                                isValid = false;
                            }

                            itemEntries.forEach((item, index) => {
                                const assetTypeSelector = item.querySelector('.asset-type-selector');
                                if (!validateField(assetTypeSelector)) isValid = false;

                                if (assetTypeSelector.value === 'new') {
                                    if (!validateField(item.querySelector('.asset-name'))) isValid = false;
                                } else {
                                    const assetMasterId = item.querySelector('.asset-master-id');
                                    const assetMasterSearch = item.querySelector('.asset-master-search');

                                    if (!assetMasterId.value) {
                                        assetMasterSearch.classList.add('border-red-500');
                                        const errorElement = assetMasterSearch.closest('.relative').querySelector('.error-message');
                                        if (errorElement) errorElement.classList.remove('hidden');
                                        isValid = false;
                                    }
                                }

                                if (!validateField(item.querySelector('.quantity'))) isValid = false;
                                if (!validateField(item.querySelector('.unit-price'))) isValid = false;
                            });

                            if (!isValid) {
                                showSweetAlert('Silakan perbaiki semua kesalahan dalam formulir.', 'error');
                                return;
                            }

                            if (isSubmitting) {
                                return;
                            }

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

                            const formData = new FormData(requestForm);
                            const data = {};

                            data.title = formData.get('title');
                            data.priority = formData.get('priority');
                            data.justification = formData.get('justification');

                            data.details = [];

                            console.log('Form data before processing:', {
                                title: data.title,
                                priority: data.priority,
                                justification: data.justification
                            });

                            itemContainer.querySelectorAll('.item-entry').forEach((item, index) => {
                                const detailObj = {};
                                const assetTypeSelector = item.querySelector('.asset-type-selector');

                                if (assetTypeSelector.value === 'new') {
                                    detailObj.asset_name = item.querySelector('.asset-name').value;
                                } else {
                                    const assetMasterId = Number(item.querySelector('.asset-master-id').value);
                                    if (assetMasterId) {
                                        detailObj.asset_master_id = assetMasterId;
                                    } else {
                                        return;
                                    }
                                }

                                detailObj.quantity = Number(item.querySelector('.quantity').value);
                                const unitPriceInput = item.querySelector('.unit-price');
                                if (unitPriceInput && unitPriceInput.value) {
                                    const numericValue = unitPriceInput.value.replace(/\./g, '').replace(',', '.');
                                    detailObj.estimated_unit_price = Number(numericValue);
                                }

                                const specs = item.querySelector('.specifications').value;
                                if (specs) detailObj.specifications = specs;

                                const notes = item.querySelector('.notes').value;
                                if (notes) detailObj.notes = notes;

                                data.details.push(detailObj);
                            });

                            console.log('Final data to be sent:', data);

                            const isUpdate = procurementId ? true : false;
                            const url = isUpdate
                                ? `/procurement/request/${procurementId}`
                                : '/procurement/request';
                            const method = isUpdate ? 'PUT' : 'POST';
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                            console.log('Request details:', {
                                url: url,
                                method: method,
                                isUpdate: isUpdate,
                                hasCSRFToken: !!csrfToken
                            });

                            fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify(data)
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                    }
                                    return response.json();
                                })
                                .then(result => {
                                    console.log('API Response:', result);

                                    if (result.success) {
                                        showSweetAlert(
                                            isUpdate ? 'Pengadaan berhasil diperbarui' : 'Pengadaan berhasil dibuat',
                                            'success',
                                            {
                                                timer: 1500,
                                                timerProgressBar: true,
                                                showConfirmButton: false,
                                                didOpen: () => {
                                                    isNavigatingAway = true;
                                                },
                                                willClose: () => {
                                                    window.location.href = '{{ route("procurement.request") }}';
                                                }
                                            }
                                        );
                                    } else {
                                        isSubmitting = false;
                                        submitButton.disabled = false;
                                        submitButton.innerHTML = originalButtonText;

                                        const errorData = result.errors || {};
                                        const errorMessage = result.message || 'Terjadi kesalahan saat memproses permintaan Anda';

                                        let displayMessage = errorMessage;
                                        let errorList = [];

                                        if (Array.isArray(errorData)) {
                                            errorData.forEach(error => {
                                                if (error.path && error.message) {
                                                    errorList.push(`${error.message}`);

                                                    if (error.path === 'title') {
                                                        highlightFieldError('title', error.message);
                                                    } else if (error.path === 'priority') {
                                                        highlightFieldError('priority', error.message);
                                                    } else if (error.path === 'justification') {
                                                        highlightFieldError('justification', error.message);
                                                    } else if (error.path.startsWith('details')) {
                                                        highlightDetailsFieldError(error.path, error.message);
                                                    }
                                                } else if (typeof error === 'string') {
                                                    errorList.push(error);
                                                }
                                            });
                                        }
                                        else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                            Object.entries(errorData).forEach(([field, errors]) => {
                                                if (field === 'title' || field === 'priority' || field === 'justification') {
                                                    highlightFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                                }

                                                if (field.includes('details.')) {
                                                    highlightDetailsFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                                }

                                                if (Array.isArray(errors)) {
                                                    errors.forEach(err => {
                                                        errorList.push(`${err}`);
                                                    });
                                                } else if (typeof errors === 'string') {
                                                    errorList.push(`${errors}`);
                                                }
                                            });
                                        } else if (typeof errorData === 'string') {
                                            errorMessage = errorData;
                                        }

                                        if (errorList.length > 0) {
                                            displayMessage += '<ul class="mt-2 list-disc pl-5">';
                                            errorList.forEach(err => {
                                                displayMessage += `<li>${err}</li>`;
                                            });
                                            displayMessage += '</ul>';
                                        }

                                        showSweetAlert(displayMessage, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Network/Fetch Error:', error);
                                    isSubmitting = false;
                                    submitButton.disabled = false;
                                    submitButton.innerHTML = originalButtonText;

                                    let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda.';

                                    if (error.message.includes('HTTP 422')) {
                                        errorMessage = 'Data yang dikirim tidak valid. Silakan periksa kembali formulir Anda.';
                                    } else if (error.message.includes('HTTP 500')) {
                                        errorMessage = 'Terjadi kesalahan server. Silakan coba lagi dalam beberapa saat.';
                                    } else if (error.message.includes('HTTP 403')) {
                                        errorMessage = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
                                    } else if (error.message.includes('Failed to fetch') || error.message.includes('Network')) {
                                        errorMessage = 'Koneksi jaringan bermasalah. Silakan periksa koneksi internet Anda.';
                                    }

                                    showSweetAlert(errorMessage, 'error', {
                                        title: 'Gagal Memproses Permintaan',
                                        footer: 'Jika masalah berlanjut, silakan hubungi administrator sistem'
                                    });
                                });
                        });

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

                        function highlightDetailsFieldError(fieldPath, errorMessage) {
                            const parts = fieldPath.split('.');
                            if (parts.length >= 3) {
                                const index = parseInt(parts[1]);
                                const subField = parts[2];
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
                                showSweetAlert('Error: ' + errorMessage, 'error');
                            }
                        }

                        window.formatCurrency = function (input, blur) {
                            let input_val = input.value;

                            if (input_val === "") { return; }

                            if (input_val.indexOf(",") >= 0) {
                                var decimal_pos = input_val.indexOf(",");

                                var left_side = input_val.substring(0, decimal_pos);
                                var right_side = input_val.substring(decimal_pos);

                                left_side = left_side.replace(/\D/g, "");
                                right_side = right_side.replace(/\D/g, "");

                                right_side = right_side.substring(0, 2);

                                left_side = left_side.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                                input_val = left_side + "," + right_side;
                            } else {
                                input_val = input_val.replace(/\D/g, "");

                                input_val = input_val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                                if (blur === "blur") {
                                    input_val += ",00";
                                }
                            }

                            input.value = input_val;
                        };

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
                                window.location.href = '{{ route("procurement.request") }}';
                            }
                        });
                    });
                </script>
    @endpush
@endsection
