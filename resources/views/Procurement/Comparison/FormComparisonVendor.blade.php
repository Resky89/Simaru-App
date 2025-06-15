@extends('Layout.app')

@section('title', isset($vendorOffer) || request()->has('agreement_id') ? 'Edit Penawaran Vendor' : 'Tambah Penawaran Vendor')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
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
                <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                    {{ isset($vendorOffer) || request()->has('agreement_id') ? 'EDIT PENAWARAN VENDOR' : 'TAMBAH PENAWARAN VENDOR' }}
                </h1>
            </div>
        </div>

        <!-- Form -->
        @if((isset($vendorOffer) || request()->has('agreement_id')) && hasPermission('price-comparison:vendor-offer:edit') || (!isset($vendorOffer) && !request()->has('agreement_id') && hasPermission('price-comparison:vendor-offer:create')))
            <form id="vendorQuotationForm" class="w-full space-y-6" data-no-loading>
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" name="comparison_id" id="comparison_id"
                    value="{{ $comparison_id ?? request()->route('id') }}">
                <!-- Add a hidden field for agreement_id if it exists -->
                @if(isset($vendorOffer) && isset($vendorOffer->agreement_id))
                    <input type="hidden" name="agreement_id" id="agreement_id" value="{{ $vendorOffer->agreement_id }}">
                @endif

                <!-- Vendor -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">Vendor <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="vendor_search"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Cari vendor..." autocomplete="off"
                            value="{{ $vendorOffer->vendor->vendor_name ?? '' }}">
                        <input type="hidden" name="vendor_id" id="selected_vendor_id"
                            value="{{ $vendorOffer->vendor->vendor_id ?? '' }}">
                        <div id="vendor_results"
                            class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300 hidden">
                        </div>
                    </div>
                    <div id="vendor_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Payment Terms -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">Syarat Pembayaran</label>
                    <textarea name="payment_terms" id="payment_terms"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Syarat Pembayaran" rows="3">{{ $vendorOffer->payment_terms ?? '' }}</textarea>
                    <div id="payment_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Delivery Terms -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">Syarat Pengiriman</label>
                    <textarea name="delivery_terms" id="delivery_terms"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Syarat Pengiriman" rows="3">{{ $vendorOffer->delivery_terms ?? '' }}</textarea>
                    <div id="delivery_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">Catatan</label>
                    <textarea name="notes" id="notes"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Catatan Tambahan" rows="2">{{ $vendorOffer->notes ?? '' }}</textarea>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <label class="block text-base font-semibold text-[#666666]">Daftar Aset</label>

                    <div class="overflow-x-auto">
                        <table class="w-full" id="itemsTable">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Jml</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Harga Satuan
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="items_container">
                                <!-- Items will be loaded dynamically -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="3" class="p-3 text-center text-gray-500">Memuat item...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="items_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 mt-8">
                    <button type="submit"
                        class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                        {{ isset($vendorOffer) || request()->has('offer_id') ? 'PERBARUI' : 'KIRIM' }}
                    </button>
                </div>
            </form>
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                <p>Maaf, Anda tidak memiliki izin untuk
                    {{ isset($vendorOffer) || request()->has('agreement_id') ? 'mengedit' : 'menambahkan' }}
                    penawaran vendor.
                </p>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('vendorQuotationForm');
            if (!form) {
                return;
            }

            const comparisonId = document.getElementById('comparison_id').value;
            const vendorOfferId = document.getElementById('vendor_offer_id')?.value;
            const isEditMode = !!vendorOfferId;
            let comparisonItems = [];
            let isSubmitting = false;
            let isNavigatingAway = false;
            let formHasBeenFilled = false;
            const urlParams = new URLSearchParams(window.location.search);
            const agreementIdFromUrl = urlParams.get('agreement_id');
            let vendorOfferIdsMap = new Map();
            for (const [key, value] of urlParams.entries()) {
                if (key.startsWith('vo_')) {
                    const itemId = parseInt(key.substring(3), 10);
                    if (!isNaN(itemId)) {
                        vendorOfferIdsMap.set(itemId, parseInt(value, 10));
                    }
                }
            }

            if (agreementIdFromUrl) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'agreement_id';
                hiddenInput.id = 'agreement_id';
                hiddenInput.value = agreementIdFromUrl;
                form.appendChild(hiddenInput);

                loadVendorOfferData(agreementIdFromUrl, vendorOfferIdsMap);
            } else {
                loadComparisonData();
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

                if (type === 'success' && options.timer === undefined) {
                    mergedOptions.timer = 2500;
                    mergedOptions.timerProgressBar = true;
                } else if (type === 'error' && options.showCloseButton === undefined) {
                    mergedOptions.confirmButtonColor = '#d33';
                    mergedOptions.showCloseButton = true;
                }

                if (!document.getElementById('swal-custom-styles')) {
                    const styleTag = document.createElement('style');
                    styleTag.id = 'swal-custom-styles';
                    styleTag.innerHTML = `
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

            function showToast(message, type = 'success') {
                showSweetAlert(message, type);
            }

            @if(session('success'))
                showSweetAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showSweetAlert("{{ session('error') }}", 'error');
            @endif

            function formHasChanges() {
                return formHasBeenFilled ||
                    document.getElementById('vendor_search').value.trim() ||
                    document.getElementById('payment_terms').value.trim() ||
                    document.getElementById('delivery_terms').value.trim() ||
                    document.getElementById('notes').value.trim();
            }

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

            function validateField(field) {
                if (field.tagName.toLowerCase() === 'select') {
                    if (!field.value) {
                        field.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        return false;
                    } else {
                        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                        return true;
                    }
                } else {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        return false;
                    } else {
                        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                        return true;
                    }
                }
            }

            function resetValidationErrors() {
                const errorElements = document.querySelectorAll('[id$="_error"]');
                errorElements.forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
            }

            function showValidationErrors(errors) {
                resetValidationErrors();

                for (const [field, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`${field.replace(/\./g, '_')}_error`);
                    if (errorElement) {
                        errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                        errorElement.classList.remove('hidden');
                    } else if (field === 'items') {
                        const itemsErrorElement = document.getElementById('items_error');
                        if (itemsErrorElement) {
                            itemsErrorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                            itemsErrorElement.classList.remove('hidden');
                        }
                    }
                }
            }

            function debounce(func, wait, immediate) {
                let timeout;
                return function () {
                    const context = this, args = arguments;
                    const later = function () {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    const callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            }

            let allVendors = [];
            const vendorSearchInput = document.getElementById('vendor_search');
            const vendorIdInput = document.getElementById('selected_vendor_id');
            const vendorResults = document.getElementById('vendor_results');

            loadAllVendors();
            loadComparisonData();

            vendorSearchInput?.addEventListener('focus', function () {
                filterAndDisplayVendors(this.value.trim());
                vendorResults.style.display = 'block';
            });

            document.addEventListener('click', function (e) {
                if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                    vendorResults.style.display = 'none';
                }
            });

            vendorSearchInput?.addEventListener('input', debounce(function () {
                const searchTerm = this.value.trim();
                filterAndDisplayVendors(searchTerm);
            }, 300));

            function loadComparisonData(itemPrices = new Map(), vendorOfferData = null, agreementId = null) {
                const itemsContainer = document.getElementById('items_container');
                itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">Memuat item...</td></tr>';

                const isEditMode = !!agreementId;

                fetch(`/procurement/detail-comparison/${comparisonId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.errors?.general || 'Gagal memuat data perbandingan');
                        }

                        comparisonItems = data.data.items || [];

                        itemsContainer.innerHTML = '';

                        if (comparisonItems.length === 0) {
                            itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">Tidak ada item yang ditemukan</td></tr>';
                            return;
                        }

                        const selectedVendorId = document.getElementById('selected_vendor_id').value;

                        comparisonItems.forEach((item, index) => {
                            const tr = document.createElement('tr');
                            tr.className = 'border-t border-[#EEF1F4]';

                            const nameCell = document.createElement('td');
                            nameCell.className = 'p-3 text-xs text-[#666666]';
                            nameCell.textContent = item.procurement_item_name;

                            const qtyCell = document.createElement('td');
                            qtyCell.className = 'p-3 text-xs text-center text-[#666666]';
                            qtyCell.textContent = item.quantity;

                            const priceCell = document.createElement('td');
                            priceCell.className = 'p-3';

                            const priceInput = document.createElement('input');
                            priceInput.type = 'text';
                            priceInput.className = 'w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200';
                            priceInput.placeholder = 'Harga Satuan';

                            const itemId = parseInt(item.price_comparison_item_id);
                            priceInput.setAttribute('data-price-comparison-item-id', itemId);
                            const priceData = itemPrices.get(itemId);

                            if (priceData && priceData.price !== undefined) {
                                priceInput.value = Number(priceData.price).toLocaleString('id-ID');

                                if (priceData.vendor_offer_id) {
                                    priceInput.setAttribute('data-vendor-offer-id', priceData.vendor_offer_id);
                                }
                                else if (itemPrices instanceof Map && itemPrices.has(itemId) && typeof itemPrices.get(itemId) === 'number') {
                                    const voId = itemPrices.get(itemId);
                                    priceInput.setAttribute('data-vendor-offer-id', voId);
                                }
                            }
                            else if (item.vendor_offers && item.vendor_offers.length > 0 && selectedVendorId) {
                                const vendorOffer = item.vendor_offers.find(
                                    offer => offer.vendor && parseInt(offer.vendor.vendor_id) === parseInt(selectedVendorId)
                                );

                                if (vendorOffer && vendorOffer.unit_price !== undefined) {
                                    priceInput.value = Number(vendorOffer.unit_price).toLocaleString('id-ID');
                                    if (vendorOffer.vendor_offer_id) {
                                        priceInput.setAttribute('data-vendor-offer-id', vendorOffer.vendor_offer_id);
                                    }
                                }
                            }
                            else if (itemPrices instanceof Map && itemPrices.has(itemId) && typeof itemPrices.get(itemId) === 'number') {
                                const voId = itemPrices.get(itemId);
                                priceInput.setAttribute('data-vendor-offer-id', voId);
                            }

                            priceInput.addEventListener('input', function (e) {
                                let value = e.target.value.replace(/[^\d]/g, '');
                                if (value) {
                                    value = parseInt(value, 10);
                                    e.target.value = value.toLocaleString('id-ID');
                                } else {
                                    e.target.value = '';
                                }
                            });

                            priceCell.appendChild(priceInput);

                            tr.appendChild(nameCell);
                            tr.appendChild(qtyCell);
                            tr.appendChild(priceCell);

                            itemsContainer.appendChild(tr);
                        });

                        if (isEditMode) {
                            const inputs = document.querySelectorAll('input[data-price-comparison-item-id]');
                            const missingIds = [];

                            inputs.forEach(input => {
                                const priceComparisonItemId = input.getAttribute('data-price-comparison-item-id');
                                const vendorOfferId = input.getAttribute('data-vendor-offer-id');

                                if (!vendorOfferId) {
                                    missingIds.push(priceComparisonItemId);
                                }
                            });

                            if (missingIds.length > 0) {
                                console.warn(`Peringatan: ${missingIds.length} item tidak memiliki vendor_offer_id:`, missingIds);
                                showSweetAlert(`Peringatan: ${missingIds.length} item tidak memiliki vendor_offer_id. Hal ini dapat menyebabkan masalah saat menyimpan.`, 'error');
                            }
                        }
                    })
                    .catch(error => {
                        itemsContainer.innerHTML = `<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-red-500">Gagal memuat item: ${error.message}</td></tr>`;
                        showSweetAlert('Gagal memuat data perbandingan: ' + error.message, 'error');
                    });
            }

            function loadAllVendors() {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                vendorResults.style.display = 'block';

                const cachedVendors = localStorage.getItem('allVendors');
                if (cachedVendors) {
                    try {
                        allVendors = JSON.parse(cachedVendors);

                        fetchAllVendors();

                        return;
                    } catch (e) {
                        console.error('Error parsing cached vendors:', e);
                    }
                }

                fetchAllVendors();
            }

            function fetchAllVendors() {
                let page = 1;
                allVendors = [];

                function fetchPage(page) {
                    if (page === 1) {
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    } else {
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor (halaman ' + page + ')...</div>';
                    }

                    fetch(`/vendors?json=true&page=${page}&limit=100`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Server responded with status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            let vendors = [];
                            let pagination = null;

                            if (Array.isArray(data)) {
                                vendors = data;
                            } else if (data.vendors && Array.isArray(data.vendors)) {
                                vendors = data.vendors;
                                pagination = data.pagination;
                            } else if (data.data && Array.isArray(data.data)) {
                                vendors = data.data;
                                pagination = data.pagination;
                            }

                            allVendors = [...allVendors, ...vendors];

                            const hasNextPage = pagination && pagination.has_next;

                            if (hasNextPage) {
                                fetchPage(page + 1);
                            } else {
                                try {
                                    localStorage.setItem('allVendors', JSON.stringify(allVendors));
                                } catch (e) {
                                    console.error('Error caching vendors:', e);
                                }

                                if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                    filterAndDisplayVendors(vendorSearchInput.value.trim());
                                } else {
                                    vendorResults.style.display = 'none';
                                }
                            }
                        })
                        .catch(error => {
                            console.error(`Error fetching vendors page ${page}:`, error);
                            vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';

                            if (allVendors.length > 0) {
                                filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');
                            }
                        });
                }

                fetchPage(page);
            }

            function filterAndDisplayVendors(searchTerm) {
                vendorResults.style.display = 'block';

                if (searchTerm && searchTerm.length > 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Mencari vendor...</div>';
                }

                if (allVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    return;
                }

                let filteredVendors = allVendors;
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    filteredVendors = allVendors.filter(vendor =>
                        vendor.vendor_name?.toLowerCase().includes(term)
                    );
                }

                if (searchTerm) {
                    filteredVendors.sort((a, b) => {
                        if (a.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return -1;
                        if (b.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return 1;

                        const aStarts = a.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        const bStarts = b.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        if (aStarts && !bStarts) return -1;
                        if (bStarts && !aStarts) return 1;

                        return a.vendor_name.localeCompare(b.vendor_name);
                    });
                }

                const displayVendors = filteredVendors.slice(0, 20);

                vendorResults.innerHTML = '';

                if (displayVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada vendor yang ditemukan</div>';
                    return;
                }

                displayVendors.forEach((vendor, index) => {
                    const div = document.createElement('div');
                    div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                    div.textContent = vendor.vendor_name;
                    div.setAttribute('data-id', vendor.vendor_id);
                    div.style.animationDelay = `${index * 30}ms`;

                    div.addEventListener('click', function () {
                        vendorIdInput.value = this.getAttribute('data-id');
                        vendorSearchInput.value = this.textContent;
                        vendorResults.style.display = 'none';

                        if (isEditMode) {
                            loadComparisonData();
                        }
                    });

                    vendorResults.appendChild(div);
                });

                if (filteredVendors.length > 20) {
                    const countDiv = document.createElement('div');
                    countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                    countDiv.textContent = `Menampilkan 20 dari ${filteredVendors.length} vendor`;
                    vendorResults.appendChild(countDiv);
                }
            }

            if (form) {
                let isSubmitting = false;
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (isSubmitting) {
                        return;
                    }

                    const allFields = form.querySelectorAll('input, select, textarea');
                    allFields.forEach(field => {
                        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                    });
                    document.querySelectorAll('[id$="_error"]').forEach(el => {
                        el.textContent = '';
                        el.classList.add('hidden');
                    });

                    const vendorId = document.getElementById('selected_vendor_id').value;
                    if (!vendorId) {
                        document.getElementById('vendor_search').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        document.getElementById('vendor_error').textContent = 'Silakan pilih vendor';
                        document.getElementById('vendor_error').classList.remove('hidden');
                        showSweetAlert('Silakan pilih vendor sebelum mengirim.', 'error');
                        return;
                    }

                    const paymentTerms = document.getElementById('payment_terms').value.trim();
                    const deliveryTerms = document.getElementById('delivery_terms').value.trim();
                    const itemPrices = [];
                    const priceInputs = document.querySelectorAll('table tbody tr input');
                    let hasErroredItem = false;
                    const agreementId = document.getElementById('agreement_id')?.value;
                    const isUpdate = !!agreementId;

                    priceInputs.forEach((input, index) => {
                        const value = input.value.trim().replace(/[^\d]/g, '');
                        const priceComparisonItemId = input.getAttribute('data-price-comparison-item-id');
                        if (!priceComparisonItemId) {
                            hasErroredItem = true;
                            return;
                        }

                        if (value) {
                            const unitPrice = parseInt(value.replace(/[^\d]/g, ''), 10);

                            const itemData = {
                                price_comparison_item_id: parseInt(priceComparisonItemId, 10),
                                unit_price: unitPrice
                            };

                            const vendorOfferId = input.getAttribute('data-vendor-offer-id');
                            if (vendorOfferId) {
                                itemData.vendor_offer_id = parseInt(vendorOfferId, 10);
                            } else if (isUpdate) {
                                console.error(`Missing vendor_offer_id for item ${priceComparisonItemId} during update`);
                                input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                hasErroredItem = true;
                            }

                            itemPrices.push(itemData);
                        }
                    });

                    if (hasErroredItem) {
                        if (isUpdate) {
                            showSweetAlert('Beberapa item tidak memiliki vendor_offer_id. Hal ini diperlukan untuk operasi update. Silakan refresh dan coba lagi.', 'error');
                        } else {
                            showSweetAlert('Beberapa item tidak memiliki data yang diperlukan. Silakan coba lagi.', 'error');
                        }
                        return;
                    }

                    const comparisonId = document.getElementById('comparison_id').value;

                    if (!comparisonId) {
                        showSweetAlert('ID perbandingan tidak ditemukan. Silakan coba lagi atau hubungi dukungan.', 'error');
                        return;
                    }

                    const requestData = {
                        comparison_id: parseInt(comparisonId, 10),
                        vendor_id: parseInt(vendorId, 10)
                    };

                    if (paymentTerms) {
                        requestData.payment_terms = paymentTerms;
                    }

                    if (deliveryTerms) {
                        requestData.delivery_terms = deliveryTerms;
                    }

                    const notes = document.getElementById('notes')?.value.trim();
                    if (notes) {
                        requestData.notes = notes;
                    }

                    if (itemPrices.length > 0) {
                        requestData.items = itemPrices;
                    } else {
                        requestData.items = [];
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    isSubmitting = true;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.textContent;
                    submitBtn.innerHTML = `
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    MENYIMPAN...
                                `;
                    submitBtn.disabled = true;

                    let endpoint = '/procurement/price-comparison/vendor-offer';
                    let method = 'POST';

                    if (agreementId) {
                        endpoint = `/procurement/price-comparison/vendor-offer/${agreementId}`;
                        method = 'PUT';
                    }

                    fetch(endpoint, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw { status: response.status, data: data };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showSweetAlert(data.message || 'Penawaran vendor berhasil disimpan!', 'success', {
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        isNavigatingAway = true;
                                    },
                                    willClose: () => {
                                        window.location.href = data.redirect_url ||
                                            `{{ route('procurement.detail-comparison', ['id' => '_ID_']) }}`.replace('_ID_', comparisonId);
                                    }
                                });
                            } else {
                                isSubmitting = false;
                                submitBtn.innerHTML = originalBtnText;
                                submitBtn.disabled = false;

                                if (data.errors) {
                                    let errorMessage = '';

                                    if (Array.isArray(data.errors)) {
                                        errorMessage = '<ul>';

                                        data.errors.forEach(err => {
                                            const fieldPath = err.path || '';
                                            const message = err.message || 'Unknown error';
                                            errorMessage += `<li><strong>${fieldPath}</strong>: ${message}</li>`;
                                            const fieldElement = document.getElementById(fieldPath);
                                            if (fieldElement) {
                                                fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                            }
                                            const errorElement = document.getElementById(`${fieldPath}_error`);
                                            if (errorElement) {
                                                errorElement.textContent = message;
                                                errorElement.classList.remove('hidden');
                                            }
                                        });

                                        errorMessage += '</ul>';

                                        showSweetAlert(errorMessage, 'error');
                                    } else if (typeof data.errors === 'object') {
                                        Object.entries(data.errors).forEach(([field, messages]) => {
                                            const fieldElement = document.getElementById(field);
                                            if (fieldElement) {
                                                fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                            }

                                            const errorElement = document.getElementById(`${field}_error`);
                                            if (errorElement) {
                                                errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                                                errorElement.classList.remove('hidden');
                                            }
                                        });

                                        showSweetAlert(data.errors?.general || data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                    } else {
                                        showSweetAlert(data.errors?.general || data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                    }
                                } else {
                                    showSweetAlert(data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            isSubmitting = false;
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;

                            if (error.data && error.data.errors) {
                                const errorData = error.data.errors;
                                let errorMessage = '';

                                if (Array.isArray(errorData)) {
                                    errorMessage = '<ul>';

                                    errorData.forEach(err => {
                                        const fieldPath = err.path || '';
                                        const message = err.message || 'Unknown error';

                                        errorMessage += `<li><strong>${fieldPath}</strong>: ${message}</li>`;

                                        const fieldElement = document.getElementById(fieldPath);
                                        if (fieldElement) {
                                            fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                        }

                                        const errorElement = document.getElementById(`${fieldPath}_error`);
                                        if (errorElement) {
                                            errorElement.textContent = message;
                                            errorElement.classList.remove('hidden');
                                        }
                                    });

                                    errorMessage += '</ul>';
                                } else if (typeof errorData === 'object') {
                                    errorMessage += '<ul>';
                                    Object.entries(errorData).forEach(([field, messages]) => {
                                        const message = Array.isArray(messages) ? messages.join(', ') : messages;
                                        errorMessage += `<li><strong>${field}</strong>: ${message}</li>`;

                                        const fieldElement = document.getElementById(field);
                                        if (fieldElement) {
                                            fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                        }

                                        const errorElement = document.getElementById(`${field}_error`);
                                        if (errorElement) {
                                            errorElement.textContent = message;
                                            errorElement.classList.remove('hidden');
                                        }
                                    });
                                    errorMessage += '</ul>';
                                } else {
                                    errorMessage = typeof errorData === 'string' ? errorData : 'Terjadi kesalahan saat menyimpan penawaran vendor.';
                                }

                                showSweetAlert(errorMessage, 'error');
                            } else if (error.message) {
                                showSweetAlert(`Terjadi kesalahan: ${error.message}`, 'error');
                            } else {
                                showSweetAlert('Terjadi kesalahan saat menyimpan penawaran vendor. Silakan coba lagi.', 'error');
                            }
                        });
                });
            }

            document.head.insertAdjacentHTML('beforeend', `
                            <style>
                                @keyframes slideInRight {
                                    from { transform: translateX(100%); }
                                    to { transform: translateX(0); }
                                }
                                .animate-slide-in-right {
                                    animation: slideInRight 0.3s ease-out forwards;
                                }

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
                            </style>
                        `);

            function loadVendorOfferData(agreementId, vendorOfferIdsMap) {
                fetch(`/procurement/price-comparison/vendor-offer/${agreementId}?use_agreement_id=true&detailed=true`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.errors?.general || 'Gagal memuat data penawaran vendor');
                        }

                        const vendorOffer = data.data;

                        if (vendorOffer.vendor) {
                            document.getElementById('vendor_search').value = vendorOffer.vendor.vendor_name;
                            document.getElementById('selected_vendor_id').value = vendorOffer.vendor.vendor_id;
                        }

                        document.getElementById('payment_terms').value = vendorOffer.payment_terms || '';
                        document.getElementById('delivery_terms').value = vendorOffer.delivery_terms || '';
                        document.getElementById('notes').value = vendorOffer.notes || '';

                        const itemPrices = new Map();

                        if (vendorOffer.items && Array.isArray(vendorOffer.items) && vendorOffer.items.length > 0) {
                            vendorOffer.items.forEach(item => {
                                if (item.price_comparison_item_id && item.unit_price !== undefined) {
                                    const itemId = parseInt(item.price_comparison_item_id, 10);
                                    const vendorOfferId = vendorOfferIdsMap.has(itemId)
                                        ? vendorOfferIdsMap.get(itemId)
                                        : (item.vendor_offer_id || null);

                                    itemPrices.set(
                                        itemId,
                                        {
                                            price: item.unit_price,
                                            price_comparison_item_id: item.price_comparison_item_id,
                                            vendor_offer_id: vendorOfferId
                                        }
                                    );
                                }
                            });
                        }

                        loadComparisonData(itemPrices, vendorOffer, parseInt(agreementId, 10));
                    })
                    .catch(error => {
                        showSweetAlert('Gagal memuat data penawaran vendor: ' + error.message, 'error');
                        loadComparisonData(vendorOfferIdsMap);
                    });
            }

            const backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.addEventListener('click', function (e) {
                    const comparisonId = document.getElementById('comparison_id').value;
                    const detailUrl = '{{ route("procurement.detail-comparison", ["id" => "__ID__"]) }}'.replace('__ID__', comparisonId);

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
                                window.location.href = detailUrl;
                            }
                        });
                    } else {
                        window.location.href = detailUrl;
                    }
                });
            }
        });
    </script>

    <style>
        .vendor-item {
            opacity: 0;
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out forwards;
        }
    </style>
@endpush