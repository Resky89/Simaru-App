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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                async function fetchAssets(searchTerm = '', page = 1) {
                    const loadingElements = document.querySelectorAll('.asset-loading');
                    loadingElements.forEach(loading => {
                        loading.style.display = 'block';
                    });

                    let url = '{{ route("assets") }}';
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
                            const { results, hasMore: newHasMore } = await fetchAssets(searchTerm, currentPage);

                            updateSelectedAssetIds();
                            const currentAssetId = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                            const availableAssets = results.filter(asset => {
                                const assetId = parseInt(asset.asset_id);
                                return !selectedAssetIds.has(assetId) || (currentAssetId === assetId);
                            });

                            availableAssets.forEach(asset => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                li.textContent = `${asset.asset_master.asset_name || 'Aset tidak dikenal'} (${asset.asset_code || 'N/A'})`;
                                li.setAttribute('data-id', asset.asset_id);
                                li.setAttribute('data-name', asset.asset_name || 'Aset tidak dikenal');
                                li.setAttribute('data-code', asset.asset_code || 'N/A');

                                li.addEventListener('click', function () {
                                    const clickedId = parseInt(this.getAttribute('data-id'));
                                    updateSelectedAssetIds();
                                    const current = hiddenInput.value ? parseInt(hiddenInput.value) : null;

                                    if (selectedAssetIds.has(clickedId) && clickedId !== current) {
                                        showSweetAlert('Aset ini sudah dipilih di item lain.', 'warning');
                                        return;
                                    }

                                    searchInput.value = `${this.getAttribute('data-name')} (${this.getAttribute('data-code')})`;
                                    hiddenInput.value = this.getAttribute('data-id');
                                    dropdown.classList.add('hidden');

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
                    .then(response => response.json())
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
                                });
                            } else {
                                // Add one empty item if no items exist
                                addItemEntry(0);
                            }

                            updateDeleteButtons();
                        } else {
                            showSweetAlert('Gagal memuat data berita acara', 'error');
                            window.location.href = '{{ route("official-report.index") }}';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showSweetAlert('Gagal memuat data berita acara', 'error');
                        window.location.href = '{{ route("official-report.index") }}';
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Asset Selection -->
                            <div class="space-y-2 asset-container">
                                <label class="block text-base font-medium text-[#666666]">Pilih Aset <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" class="asset-search w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Cari aset..." autocomplete="off" value="${data && data.asset ? `${data.asset.asset_name} (${data.asset.asset_code})` : ''}">
                                    <input type="hidden" name="items[${index}][asset_id]" class="asset-id" value="${data && data.asset ? data.asset.asset_id : ''}">
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
                        notes: document.getElementById('notes').value || '',
                        items: []
                    };

                    // Collect items data
                    items.forEach((item, index) => {
                        const assetId = item.querySelector('.asset-id');
                        const itemNotes = item.querySelector('.item-notes');

                        if (assetId.value) {
                            data.items.push({
                                asset_id: parseInt(assetId.value),
                                item_notes: itemNotes.value || ''
                            });
                        }
                    });

                    const isUpdate = isEditMode && officialReportId;
                    const url = isUpdate
                        ? `{{ url('official-reports') }}/${officialReportId}`
                        : '{{ route("official-report.store") }}';
                    const method = isUpdate ? 'PUT' : 'POST';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': method
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
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

                                const errorData = result.errors || result.message || 'Terjadi kesalahan';
                                let errorMessage = typeof errorData === 'string' ? errorData : 'Terjadi kesalahan saat memproses permintaan Anda';

                                showSweetAlert(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            isSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;

                            showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.', 'error');
                        });
                });

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
