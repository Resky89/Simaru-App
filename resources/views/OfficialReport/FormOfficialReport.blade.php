@extends('Layout.app')

@section('title', 'Formulir Berita Acara')

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
                <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]" id="formTitle">
                    TAMBAH BERITA ACARA
                </h1>
            </div>
        </div>

        <!-- Form -->
        @if(hasPermission('official-report:create') || hasPermission('official-report:edit'))
            <form id="officialReportForm" class="w-full space-y-6" data-no-loading>
                @csrf
                <input type="hidden" id="form_method" name="_method" value="POST">
                <input type="hidden" id="official_report_id" name="official_report_id">

                <div class="space-y-6">
                    <!-- Report Type -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Tipe Berita Acara <span
                                class="text-red-500">*</span></label>
                        <select id="report_type" name="report_type"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Pilih tipe berita acara</option>
                            <option value="DISPOSAL">Dihapuskan</option>
                            <option value="LOSS">Hilang</option>
                            <option value="FOUND">Ditemukan</option>
                        </select>
                        <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe berita acara harus dipilih</div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Catatan</label>
                        <textarea id="notes" name="notes"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Catatan" rows="3"></textarea>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <label class="block text-base font-semibold text-[#666666]">Daftar Aset</label>

                    <div id="itemContainer" class="space-y-6">
                        <!-- Items will be dynamically added here -->
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
                        SIMPAN
                    </button>
                </div>
            </form>
        @else
            <!-- No permission message -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                <p>Maaf, Anda tidak memiliki izin untuk mengelola berita acara.</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemContainer = document.getElementById('itemContainer');
                const officialReportForm = document.getElementById('officialReportForm');

                let isNavigatingAway = false;
                let selectedAssetIds = new Set();
                let itemCount = 0;

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

                        return `${day} ${month} ${year}`;
                    } catch (e) {
                        console.error('Date formatting error:', e);
                        return dateString;
                    }
                }

                // Form change detection
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
                    if (document.getElementById('report_type').value ||
                        document.getElementById('notes').value) {
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

                function updateSelectedAssetIds() {
                    selectedAssetIds.clear();
                    document.querySelectorAll('.asset-id').forEach(input => {
                        if (input.value) {
                            selectedAssetIds.add(parseInt(input.value));
                        }
                    });
                }

                async function fetchAssets(searchTerm = '', page = 1, additionalParams = {}) {
                    const loadingElements = document.querySelectorAll('.asset-loading');
                    loadingElements.forEach(loading => {
                        loading.style.display = 'block';
                    });

                    let url = '{{ route("assets") }}';
                    const params = new URLSearchParams({
                        search: searchTerm,
                        page: page,
                        limit: 20,
                        ...additionalParams
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
                        const results = data.assets || [];
                        loadingElements.forEach(loading => {
                            loading.style.display = 'none';
                        });
                        return { results, hasMore: results.length === 20 };
                    } catch (error) {
                        console.error('Error loading assets:', error);
                        loadingElements.forEach(loading => {
                            loading.style.display = 'none';
                        });
                        showSweetAlert('Gagal memuat daftar aset. Silakan coba lagi.', 'error');
                        return { results: [], hasMore: false };
                    }
                }

                function getAssetFilterParams() {
                    const reportType = document.getElementById('report_type').value;
                    const params = {};

                    switch (reportType) {
                        case 'FOUND':
                            params.current_status = 'lost';
                            break;
                        case 'LOSS':
                            params.exclude_status = 'dispose';
                            break;
                        default:
                            break;
                    }

                    return params;
                }

                function setupAssetSearch(container) {
                    const searchInput = container.querySelector('.asset-search');
                    const dropdown = container.querySelector('.asset-dropdown');
                    const list = container.querySelector('.asset-list');
                    const initialLoading = container.querySelector('.asset-loading');
                    const loadMoreLoading = container.querySelector('.asset-load-more');
                    const hiddenInput = container.querySelector('.asset-id');

                    if (!searchInput || !dropdown || !list || !initialLoading || !loadMoreLoading) return;

                    let page = 1;
                    let hasMore = true;
                    let isLoading = false;
                    let searchTerm = '';

                    async function loadAssets(newSearchTerm, reset = true) {
                        if (isLoading) return;

                        isLoading = true;

                        const currentPage = reset ? 1 : page;

                        if (reset) {
                            searchTerm = newSearchTerm;
                            list.innerHTML = '';
                            initialLoading.style.display = 'block';
                            loadMoreLoading.style.display = 'none';
                        } else {
                            initialLoading.style.display = 'none';
                            loadMoreLoading.style.display = 'block';
                        }

                        try {
                            const filterParams = getAssetFilterParams();
                            const { results, hasMore: newHasMore } = await fetchAssets(searchTerm, currentPage, filterParams);

                            updateSelectedAssetIds();
                            const currentAssetId = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                            const availableAssets = results.filter(asset => {
                                const assetId = parseInt(asset.asset_id);
                                // Pastikan asset memiliki asset_id yang valid
                                if (!asset.asset_id || isNaN(assetId) || assetId <= 0) {
                                    console.warn('Skipping asset with invalid ID:', asset);
                                    return false;
                                }
                                return !selectedAssetIds.has(assetId) || (currentAssetId === assetId);
                            });

                            availableAssets.forEach(asset => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                // Pastikan asset_master ada dan valid
                                const assetName = asset.asset_master?.asset_name || asset.asset_name || 'Aset tidak dikenal';
                                const assetCode = asset.asset_code || 'N/A';

                                li.textContent = `${assetName} (${assetCode})`;
                                li.setAttribute('data-id', asset.asset_id);
                                li.setAttribute('data-name', assetName);
                                li.setAttribute('data-code', assetCode);

                                li.addEventListener('click', function () {
                                    const clickedId = parseInt(this.getAttribute('data-id'));

                                    // Validasi clicked ID
                                    if (isNaN(clickedId) || clickedId <= 0) {
                                        showSweetAlert('Asset ID tidak valid. Silakan coba lagi.', 'error');
                                        return;
                                    }

                                    updateSelectedAssetIds();
                                    const current = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                                    if (selectedAssetIds.has(clickedId) && clickedId !== current) {
                                        showSweetAlert('Aset ini sudah dipilih di item lain.', 'warning');
                                        return;
                                    }

                                    searchInput.value = `${this.getAttribute('data-name')} (${this.getAttribute('data-code')})`;
                                    hiddenInput.value = this.getAttribute('data-id');
                                    dropdown.classList.add('hidden');

                                    // Clear any previous errors
                                    searchInput.classList.remove('border-red-500');
                                    const errorElement = searchInput.closest('.relative').querySelector('.error-message');
                                    if (errorElement) errorElement.classList.add('hidden');

                                    updateSelectedAssetIds();
                                    // Remove from other dropdowns
                                    document.querySelectorAll('.asset-list li[data-id="' + clickedId + '"]').forEach(otherLi => {
                                        const otherContainer = otherLi.closest('.asset-container');
                                        const otherHidden = otherContainer.querySelector('.asset-id');
                                        if (otherHidden.value != clickedId) {
                                            otherLi.remove();
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
                        } finally {
                            initialLoading.style.display = 'none';
                            loadMoreLoading.style.display = 'none';
                            isLoading = false;
                        }
                    }

                    searchInput.addEventListener('input', function () {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            loadAssets(this.value, true);
                        }, 300);
                    });

                    searchInput.addEventListener('focus', async function () {
                        dropdown.classList.remove('hidden');
                        if (list.innerHTML === '') {
                            await loadAssets('', true);
                        }
                    });

                    dropdown.addEventListener('scroll', () => {
                        if (dropdown.scrollTop + dropdown.clientHeight >= dropdown.scrollHeight - 10 && hasMore && !isLoading) {
                            loadAssets(searchTerm, false);
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!container.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                // Detect if we're in edit mode
                const urlSegments = window.location.pathname.split('/');
                const isEditMode = urlSegments.includes('edit');
                const officialReportId = isEditMode ? urlSegments[urlSegments.length - 2] : null;

                if (isEditMode && officialReportId) {
                    // Update UI for edit mode
                    document.getElementById('formTitle').textContent = 'EDIT BERITA ACARA';
                    document.getElementById('submitButton').textContent = 'PERBARUI';
                    document.getElementById('form_method').value = 'PUT';
                    document.getElementById('official_report_id').value = officialReportId;

                    loadOfficialReportData(officialReportId);
                } else {
                    // Add initial item for new form
                    if (itemContainer.querySelectorAll('.item-entry').length === 0) {
                        addItemEntry(0);
                        updateAssetFilterInfo(); // Update filter info for new form
                    }
                }

                function loadOfficialReportData(reportId) {
                    // Show loading state
                    const submitButton = document.getElementById('submitButton');
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        MEMUAT...
                    `;

                    fetch(`{{ url('official-reports') }}/${reportId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server tidak mengembalikan response JSON yang valid');
                        }

                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data) {
                            const report = data.data;

                            // Fill form fields
                            document.getElementById('report_type').value = report.report_type || '';
                            document.getElementById('notes').value = report.notes || '';

                            // Clear existing items
                            itemContainer.innerHTML = '';
                            itemCount = 0;

                            // Add items from API data
                            if (report.items && report.items.length > 0) {
                                report.items.forEach(item => {
                                    const newItem = addItemEntry(itemCount, {
                                        asset_id: item.asset?.asset_id,
                                        asset_name: `${item.asset?.asset_name || 'Unknown'} (${item.asset?.asset_code || 'N/A'})`,
                                        item_notes: item.item_notes
                                    });
                                    itemCount++;
                                });
                            } else {
                                // Add one empty item if no items exist
                                addItemEntry(0);
                            }

                            updateDeleteButtons();
                            updateAssetFilterInfo(); // Update filter info after loading edit data
                        } else {
                            const errorMessage = data.message || 'Gagal memuat data berita acara';
                            showSweetAlert(errorMessage, 'error', {
                                title: 'Gagal Memuat Data',
                                footer: 'Silakan coba muat ulang halaman'
                            });
                            setTimeout(() => {
                            window.location.href = '{{ route("official-report.index") }}';
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'Gagal memuat data berita acara';

                        if (error.message.includes('JSON')) {
                            errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi.';
                        } else if (error.message.includes('HTTP')) {
                            errorMessage = `Kesalahan server: ${error.message}`;
                        } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        }

                        showSweetAlert(errorMessage, 'error', {
                            title: 'Gagal Memuat Data',
                            footer: 'Silakan coba muat ulang halaman'
                        });
                        setTimeout(() => {
                        window.location.href = '{{ route("official-report.index") }}';
                        }, 2000);
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'PERBARUI';
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

                        <div class="space-y-4">
                            <!-- Asset Selection -->
                            <div class="space-y-2 asset-container">
                                <label class="block text-base font-medium text-[#666666]">Pilih Aset <span class="text-red-500">*</span></label>
                                <div class="asset-filter-info text-xs text-blue-600 mb-1 hidden"></div>
                                <div class="relative">
                                    <input type="text" class="asset-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Cari aset..." autocomplete="off" value="${data && data.asset_name ? data.asset_name : ''}">
                                    <input type="hidden" name="items[${index}][asset_id]" class="asset-id" value="${data && data.asset_id ? data.asset_id : ''}">
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Aset harus dipilih</div>

                                    <!-- Dropdown -->
                                    <div class="asset-dropdown absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                        <div class="asset-loading p-2 text-gray-500 text-center">
                                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Memuat daftar aset...</span>
                                        </div>
                                        <ul class="asset-list py-1"></ul>
                                        <div class="asset-load-more p-2 text-gray-500 text-center hidden">
                                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Memuat lebih banyak aset...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Notes -->
                            <div class="space-y-2">
                                <label class="block text-base font-medium text-[#666666]">Catatan Item</label>
                                <textarea name="items[${index}][item_notes]" class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 item-notes"
                                    placeholder="Catatan untuk aset ini..." rows="3">${data ? data.item_notes || '' : ''}</textarea>
                            </div>
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
                            updateSelectedAssetIds();
                        }
                    });

                    const assetContainer = newItem.querySelector('.asset-container');
                    setupAssetSearch(assetContainer);

                    updateSelectedAssetIds();
                    updateDeleteButtons();
                    updateAssetFilterInfo(); // Update filter info when new item is added
                    return newItem; // Return the created item for potential use in loadOfficialReportData
                }

                addItemBtn.addEventListener('click', function () {
                    const itemCount = itemContainer.querySelectorAll('.item-entry').length;
                    addItemEntry(itemCount);
                    updateDeleteButtons();
                });

                function updateInputNames() {
                    const items = itemContainer.querySelectorAll('.item-entry');
                    items.forEach((item, index) => {
                        const assetId = item.querySelector('.asset-id');
                        const itemNotes = item.querySelector('.item-notes');

                        assetId.name = `items[${index}][asset_id]`;
                        itemNotes.name = `items[${index}][item_notes]`;
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

                // Form field validation listeners
                document.getElementById('report_type').addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');

                    // Reset all asset searches when report type changes
                    resetAllAssetSearches();
                });

                function resetAllAssetSearches() {
                    document.querySelectorAll('.asset-container').forEach(container => {
                        const searchInput = container.querySelector('.asset-search');
                        const dropdown = container.querySelector('.asset-dropdown');
                        const list = container.querySelector('.asset-list');
                        const hiddenInput = container.querySelector('.asset-id');

                        if (searchInput && list && hiddenInput) {
                            // Clear current selection
                            searchInput.value = '';
                            hiddenInput.value = '';
                            list.innerHTML = '';
                            dropdown.classList.add('hidden');

                            // Update selected asset IDs
                            updateSelectedAssetIds();
                        }
                    });

                    // Update filter info for all asset containers
                    updateAssetFilterInfo();
                }

                function updateAssetFilterInfo() {
                    const reportType = document.getElementById('report_type').value;
                    let filterText = '';

                    switch (reportType) {
                        case 'FOUND':
                            filterText = '📍 Menampilkan aset dengan status: Hilang';
                            break;
                        case 'LOSS':
                            filterText = '🚫 Mengecualikan aset yang sudah dihapuskan';
                            break;
                        case 'DISPOSAL':
                            filterText = '📦 Menampilkan semua aset tersedia';
                            break;
                        default:
                            filterText = '';
                    }

                    document.querySelectorAll('.asset-filter-info').forEach(info => {
                        if (filterText) {
                            info.textContent = filterText;
                            info.classList.remove('hidden');
                        } else {
                            info.classList.add('hidden');
                        }
                    });
                }

                document.getElementById('notes').addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                // Add event listeners for item fields when they are created
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('asset-search') || e.target.classList.contains('item-notes')) {
                        e.target.classList.remove('border-red-500');
                        const errorElement = e.target.closest('.space-y-2')?.querySelector('.error-message') ||
                                             e.target.closest('.relative')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
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
                officialReportForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    let isValid = true;

                    // Validate report type
                    if (!validateField(document.getElementById('report_type'))) isValid = false;

                    // Validate items
                    const items = itemContainer.querySelectorAll('.item-entry');
                    if (items.length === 0) {
                        showSweetAlert('Minimal satu aset harus dipilih.', 'error');
                        return;
                    }

                    items.forEach((item, index) => {
                        const assetId = item.querySelector('.asset-id');
                        const assetSearch = item.querySelector('.asset-search');

                        if (!assetId.value) {
                            assetSearch.classList.add('border-red-500');
                            const errorElement = assetSearch.closest('.relative').querySelector('.error-message');
                            if (errorElement) errorElement.classList.remove('hidden');
                            isValid = false;
                        }
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

                    // Build request data
                    const data = {
                        report_type: document.getElementById('report_type').value,
                        items: []
                    };

                    // Only include notes field if it has a value
                    const notesValue = document.getElementById('notes').value.trim();
                    if (notesValue) {
                        data.notes = notesValue;
                    }

                    // Collect items data
                    items.forEach((item, index) => {
                        const assetId = item.querySelector('.asset-id');
                        const itemNotes = item.querySelector('.item-notes');

                        if (assetId.value) {
                            const parsedAssetId = parseInt(assetId.value);

                            // Validasi asset_id sebelum mengirim
                            if (isNaN(parsedAssetId) || parsedAssetId <= 0) {
                                console.error('Invalid asset_id for item', index, ':', assetId.value);
                                isValid = false;

                                const assetSearch = item.querySelector('.asset-search');
                                assetSearch.classList.add('border-red-500');
                                const errorElement = assetSearch.closest('.relative').querySelector('.error-message');
                                if (errorElement) {
                                    errorElement.textContent = 'Asset ID tidak valid';
                                    errorElement.classList.remove('hidden');
                                }
                                return;
                            }

                            const itemData = {
                                asset_id: parsedAssetId
                            };

                            // Only include item_notes if it has a non-empty value
                            const notesValue = itemNotes.value?.trim();
                            if (notesValue) {
                                itemData.item_notes = notesValue;
                            }

                            data.items.push(itemData);
                        }
                    });

                    // Validasi final sebelum submit
                    if (!isValid || data.items.length === 0) {
                        isSubmitting = false;
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;

                        if (data.items.length === 0) {
                            showSweetAlert('Minimal satu aset harus dipilih dengan benar.', 'error');
                        } else {
                            showSweetAlert('Terdapat asset ID yang tidak valid. Silakan pilih aset yang benar.', 'error');
                        }
                        return;
                    }

                    const isUpdate = isEditMode && officialReportId;

                    // Check permissions before submission
                    @if(!hasPermission('official-report:create') && !hasPermission('official-report:edit'))
                        showSweetAlert('Anda tidak memiliki izin untuk melakukan operasi ini.', 'error');
                        isSubmitting = false;
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                        return;
                    @endif

                    @if(hasPermission('official-report:create') && !hasPermission('official-report:edit'))
                        if (isUpdate) {
                            showSweetAlert('Anda tidak memiliki izin untuk mengedit berita acara.', 'error');
                            isSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                            return;
                        }
                    @endif

                    let url, httpMethod;

                    if (isUpdate) {
                        url = `{{ url('official-reports') }}/${officialReportId}`;
                        httpMethod = 'PUT';
                    } else {
                        url = '{{ route("official-report.store") }}';
                        httpMethod = 'POST';
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Validate CSRF token
                    if (!csrfToken) {
                        showSweetAlert('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.', 'error');
                        isSubmitting = false;
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                        return;
                    }

                    // Prepare fetch options
                    const fetchOptions = {
                        method: httpMethod,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    };

                    // Try direct method first, then fallback to POST with method override
                    async function submitForm() {
                        try {
                            const response = await fetch(url, fetchOptions);
                            return response;
                        } catch (error) {
                            if (isUpdate) {
                                // Fallback to POST with method override for updates
                                const fallbackOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                                        'X-HTTP-Method-Override': 'PUT'
                        },
                        body: JSON.stringify(data)
                                };

                                return await fetch(url, fallbackOptions);
                            } else {
                                throw error;
                            }
                        }
                    }

                    submitForm()
                        .then(response => {
                            // Check if response is ok and is JSON
                            if (!response.ok) {
                                // Try to get the error response body for better debugging
                                return response.text().then(text => {
                                    console.error('Error response body:', text);
                                    let errorData;
                                    try {
                                        errorData = JSON.parse(text);
                                        console.error('Parsed error data:', errorData);
                                    } catch (e) {
                                        console.error('Response is not JSON:', text);
                                        errorData = {
                                            message: text || `HTTP ${response.status}: ${response.statusText}`,
                                            status: response.status,
                                            statusText: response.statusText
                                        };
                                    }

                                    // Create a custom error with the parsed data
                                    const error = new Error('Server Error');
                                    error.responseData = errorData;
                                    error.status = response.status;
                                    throw error;
                                });
                            }

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Server tidak mengembalikan response JSON yang valid');
                            }

                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                showSweetAlert(
                                    isUpdate ? 'Berita acara berhasil diperbarui' : 'Berita acara berhasil dibuat',
                                    'success',
                                    {
                                        timer: 1500,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            isNavigatingAway = true;
                                        },
                                        willClose: () => {
                                            window.location.href = '{{ route("official-report.index") }}';
                                        }
                                    }
                                );
                            } else {
                                isSubmitting = false;
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;

                                const errorData = result.errors || [];
                                let errorMessage = result.message || 'Terjadi kesalahan saat memproses permintaan Anda:';
                                let errorList = [];

                                // Handle different error response formats
                                if (typeof errorData === 'string') {
                                    // Handle when errors is a simple string
                                    errorMessage = errorData;
                                } else if (Array.isArray(errorData)) {
                                    // Handle when errors is an array
                                    errorData.forEach(error => {
                                        if (error.path && error.message) {
                                            errorList.push(`${error.message}`);

                                            // Highlight specific fields based on error path
                                            if (error.path === 'report_type') {
                                                highlightFieldError('report_type', error.message);
                                            } else if (error.path === 'notes') {
                                                highlightFieldError('notes', error.message);
                                            } else if (error.path.startsWith('items')) {
                                                highlightItemsFieldError(error.path, error.message);
                                            }
                                        } else if (typeof error === 'string') {
                                            errorList.push(error);
                                        }
                                    });
                                } else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                    // Handle when errors is an object with field-specific errors
                                    Object.entries(errorData).forEach(([field, errors]) => {
                                        if (field === 'report_type' || field === 'notes') {
                                            highlightFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                        }

                                        if (field.includes('items.')) {
                                            highlightItemsFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                        }

                                        if (Array.isArray(errors)) {
                                            errors.forEach(err => {
                                                errorList.push(`${err}`);
                                            });
                                        } else if (typeof errors === 'string') {
                                            errorList.push(`${errors}`);
                                        }
                                    });
                                }

                                // If we have errorList items and errorData was not a simple string, format as list
                                if (errorList.length > 0 && typeof errorData !== 'string') {
                                    errorMessage += '<ul class="mt-2 list-disc pl-5">';
                                    errorList.forEach(err => {
                                        errorMessage += `<li>${err}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                } else if (errorList.length > 0 && typeof errorData === 'string') {
                                    // If errorData is string but we somehow got errorList, just use the string
                                    errorMessage = errorData;
                                }

                                showSweetAlert(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Catch block - Full error object:', error);
                            console.error('Error message:', error.message);
                            console.error('Error responseData:', error.responseData);
                            console.error('Error status:', error.status);

                            isSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;

                            let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda.';
                            let errorData = null;

                            // Check if error has responseData (from server error response)
                            if (error.responseData) {
                                errorData = error.responseData;
                            } else {
                                // Try to parse error data from thrown error message (fallback)
                                try {
                                    errorData = JSON.parse(error.message);
                                } catch (e) {
                                    // If parsing fails, errorData remains null
                                    console.log('Could not parse error message as JSON:', error.message);
                                }
                            }

                            if (errorData) {
                                // Handle server error response
                                if (errorData.errors) {
                                    let errorList = [];

                                    if (typeof errorData.errors === 'string') {
                                        // Handle when errors is a simple string
                                        errorMessage = errorData.errors;
                                    } else if (Array.isArray(errorData.errors)) {
                                        // Handle when errors is an array
                                        errorMessage = 'Terjadi kesalahan validasi:';
                                        errorData.errors.forEach(error => {
                                            if (error.path && error.message) {
                                                errorList.push(`${error.message}`);

                                                // Highlight specific fields based on error path
                                                if (error.path === 'report_type' || error.path === 'notes') {
                                                    highlightFieldError(error.path, error.message);
                                                } else if (error.path.startsWith('items')) {
                                                    highlightItemsFieldError(error.path, error.message);
                                                }
                                            } else if (typeof error === 'string') {
                                                errorList.push(error);
                                            }
                                        });
                                    } else if (typeof errorData.errors === 'object') {
                                        // Handle when errors is an object with field-specific errors
                                        errorMessage = 'Terjadi kesalahan validasi:';
                                        Object.entries(errorData.errors).forEach(([field, errors]) => {
                                            if (field === 'report_type' || field === 'notes') {
                                                highlightFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                            }

                                            if (field.includes('items.')) {
                                                highlightItemsFieldError(field, Array.isArray(errors) ? errors[0] : errors);
                                            }

                                            if (Array.isArray(errors)) {
                                                errors.forEach(err => errorList.push(`${err}`));
                                            } else if (typeof errors === 'string') {
                                                errorList.push(`${errors}`);
                                            }
                                        });
                                    }

                                    // If we have errorList items and errors was not a simple string, format as list
                                    if (errorList.length > 0 && typeof errorData.errors !== 'string') {
                                        errorMessage += '<ul class="mt-2 list-disc pl-5">';
                                        errorList.forEach(err => {
                                            errorMessage += `<li>${err}</li>`;
                                        });
                                        errorMessage += '</ul>';
                                    } else if (errorList.length > 0 && typeof errorData.errors === 'string') {
                                        // If errors is string but we somehow got errorList, just use the string
                                        errorMessage = errorData.errors;
                                    }
                                } else if (errorData.message) {
                                    errorMessage = errorData.message;
                                }
                            } else {
                                // Handle network/other errors based on error type and status
                                if (error.status) {
                                    // Handle specific HTTP status codes
                                    switch (error.status) {
                                        case 400:
                                            errorMessage = 'Permintaan tidak valid. Silakan periksa data yang dimasukkan.';
                                            break;
                                        case 401:
                                            errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.';
                                            break;
                                        case 403:
                                            errorMessage = 'Anda tidak memiliki izin untuk melakukan operasi ini.';
                                            break;
                                        case 404:
                                            errorMessage = 'Data yang diminta tidak ditemukan.';
                                            break;
                                        case 422:
                                            errorMessage = 'Data yang dimasukkan tidak valid. Silakan periksa kembali.';
                                            break;
                                        case 500:
                                            errorMessage = 'Terjadi kesalahan server internal. Silakan coba lagi atau hubungi administrator.';
                                            break;
                                        default:
                                            errorMessage = `Kesalahan server (${error.status}): ${error.statusText || 'Unknown error'}. Silakan coba lagi.`;
                                    }
                                } else if (error.message.includes('JSON')) {
                                    errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi atau hubungi administrator.';
                                } else if (error.message.includes('HTTP')) {
                                    errorMessage = `Kesalahan server: ${error.message}. Silakan coba lagi.`;
                                } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                                } else {
                                    // For any other errors, try to show the actual error message
                                    errorMessage = error.message || errorMessage;
                                }
                            }

                            showSweetAlert(errorMessage, 'error');
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

                function highlightItemsFieldError(fieldPath, errorMessage) {
                    const parts = fieldPath.split('.');
                    if (parts.length >= 3) {
                        const index = parseInt(parts[1]);
                        const subField = parts[2];
                        const items = itemContainer.querySelectorAll('.item-entry');
                        if (items[index]) {
                            let field;
                            let errorElement;

                            switch (subField) {
                                case 'asset_id':
                                    field = items[index].querySelector('.asset-search');
                                    errorElement = field?.closest('.relative')?.querySelector('.error-message');
                                    break;
                                case 'item_notes':
                                    field = items[index].querySelector('.item-notes');
                                    errorElement = field?.closest('.space-y-2')?.querySelector('.error-message');
                                    break;
                            }

                            if (field) {
                                field.classList.add('border-red-500');
                                if (errorElement) {
                                    errorElement.textContent = errorMessage;
                                    errorElement.classList.remove('hidden');
                                }
                            }
                        }
                    } else if (fieldPath === 'items') {
                        showSweetAlert('Error: ' + errorMessage, 'error');
                    }
                }

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
                                window.location.href = '{{ route("official-report.index") }}';
                            }
                        });
                    } else {
                        isNavigatingAway = true;
                        window.location.href = '{{ route("official-report.index") }}';
                    }
                });
            });
        </script>
    @endpush
@endsection
