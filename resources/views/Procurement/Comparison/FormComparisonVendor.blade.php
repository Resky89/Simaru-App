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
            // Check if the form exists based on permissions
            const form = document.getElementById('vendorQuotationForm');
            if (!form) {
                return; // Exit early if no form due to permission restrictions
            }

            const comparisonId = document.getElementById('comparison_id').value;
            const vendorOfferId = document.getElementById('vendor_offer_id')?.value;
            const isEditMode = !!vendorOfferId;
            let comparisonItems = [];

            // Add flags for form tracking and navigation
            let isSubmitting = false; // Flag to track submission status
            let isNavigatingAway = false; // Flag to track intentional navigation
            let formHasBeenFilled = false; // Flag to track form changes

            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const agreementIdFromUrl = urlParams.get('agreement_id');

            // Extract vendor_offer_ids from URL params (format: vo_{price_comparison_item_id}={vendor_offer_id})
            let vendorOfferIdsMap = new Map();
            for (const [key, value] of urlParams.entries()) {
                if (key.startsWith('vo_')) {
                    const itemId = parseInt(key.substring(3), 10);
                    if (!isNaN(itemId)) {
                        vendorOfferIdsMap.set(itemId, parseInt(value, 10));
                        console.log(`Found vendor_offer_id ${value} for item ${itemId} in URL params`);
                    }
                }
            }

            console.log(`Extracted ${vendorOfferIdsMap.size} vendor offer IDs from URL params`);

            // If we have an agreement ID from URL, load the vendor offer data
            if (agreementIdFromUrl) {
                // Set the agreement_id input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'agreement_id';
                hiddenInput.id = 'agreement_id';
                hiddenInput.value = agreementIdFromUrl;
                form.appendChild(hiddenInput);

                // Load vendor offer data using agreement ID
                loadVendorOfferData(agreementIdFromUrl, vendorOfferIdsMap);
            } else {
                // If we don't have an agreement_id, load comparison data directly
                loadComparisonData();
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
                if (type === 'success' && options.timer === undefined) {
                    // Auto close success messages after 2.5 seconds
                    mergedOptions.timer = 2500;
                    mergedOptions.timerProgressBar = true;
                } else if (type === 'error' && options.showCloseButton === undefined) {
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

            // Replace the toast notification with SweetAlert
            function showToast(message, type = 'success') {
                showSweetAlert(message, type);
            }

            // Show SweetAlert notifications for session messages on page load
            @if(session('success'))
                showSweetAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showSweetAlert("{{ session('error') }}", 'error');
            @endif

            // Helper function to check if the form has any changes
            function formHasChanges() {
                return formHasBeenFilled ||
                    document.getElementById('vendor_search').value.trim() ||
                    document.getElementById('payment_terms').value.trim() ||
                    document.getElementById('delivery_terms').value.trim() ||
                    document.getElementById('notes').value.trim();
            }

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

            // Keep a limited beforeunload for cases like tab closing, refreshing or external navigation
            // This cannot use SweetAlert due to browser security restrictions
            window.addEventListener('beforeunload', function (e) {
                // Only show if there are form changes and we're not already submitting or redirecting
                if (!isSubmitting && !isNavigatingAway && formHasChanges()) {
                    // Modern browsers will show a generic message regardless of what we set here
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

            // Function to validate a form field
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

            // Reset validation errors
            function resetValidationErrors() {
                const errorElements = document.querySelectorAll('[id$="_error"]');
                errorElements.forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
            }

            // Show validation errors
            function showValidationErrors(errors) {
                resetValidationErrors();

                for (const [field, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`${field.replace(/\./g, '_')}_error`);
                    if (errorElement) {
                        errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                        errorElement.classList.remove('hidden');
                    } else if (field === 'items') {
                        // Special handling for items array errors
                        const itemsErrorElement = document.getElementById('items_error');
                        if (itemsErrorElement) {
                            itemsErrorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                            itemsErrorElement.classList.remove('hidden');
                        }
                    }
                }
            }

            // Debounce function to limit how often a function can be called
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

            // Vendor search functionality with client-side filtering
            let allVendors = []; // Store all vendors for client-side filtering
            const vendorSearchInput = document.getElementById('vendor_search');
            const vendorIdInput = document.getElementById('selected_vendor_id');
            const vendorResults = document.getElementById('vendor_results');

            // Initial load of vendors
            loadAllVendors();

            // Load comparison data
            loadComparisonData();

            // Show/hide vendor results
            vendorSearchInput?.addEventListener('focus', function () {
                filterAndDisplayVendors(this.value.trim());
                vendorResults.style.display = 'block';
            });

            // Hide vendor results when clicking outside
            document.addEventListener('click', function (e) {
                if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                    vendorResults.style.display = 'none';
                }
            });

            // Search vendors with debounce
            vendorSearchInput?.addEventListener('input', debounce(function () {
                const searchTerm = this.value.trim();
                filterAndDisplayVendors(searchTerm);
            }, 300));

            // Load comparison data to populate items
            function loadComparisonData(itemPrices = new Map(), vendorOfferData = null, agreementId = null) {
                const itemsContainer = document.getElementById('items_container');
                itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">Memuat item...</td></tr>';

                // Set edit mode flag based on agreementId
                const isEditMode = !!agreementId;

                fetch(`/procurement/price-comparison/${comparisonId}`, {
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
                        console.log('Loaded comparison items:', comparisonItems);

                        // Clear container
                        itemsContainer.innerHTML = '';

                        if (comparisonItems.length === 0) {
                            itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">Tidak ada item yang ditemukan</td></tr>';
                            return;
                        }

                        // Get selected vendor id
                        const selectedVendorId = document.getElementById('selected_vendor_id').value;

                        // Add items to the table
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

                            // Always set the price_comparison_item_id attribute
                            const itemId = parseInt(item.price_comparison_item_id);
                            priceInput.setAttribute('data-price-comparison-item-id', itemId);

                            // Check if we have a price for this item in our price map
                            const priceData = itemPrices.get(itemId);

                            // First try to get data from itemPrices map
                            if (priceData && priceData.price !== undefined) {
                                // We found a price in our map - set the price
                                priceInput.value = Number(priceData.price).toLocaleString('id-ID');

                                // Store the vendor_offer_id if it exists
                                if (priceData.vendor_offer_id) {
                                    priceInput.setAttribute('data-vendor-offer-id', priceData.vendor_offer_id);
                                    console.log(`Set vendor_offer_id ${priceData.vendor_offer_id} for item ${itemId} from price map`);
                                }
                                // If itemPrices is a Map of vendor_offer_ids directly (from URL)
                                else if (itemPrices instanceof Map && itemPrices.has(itemId) && typeof itemPrices.get(itemId) === 'number') {
                                    const voId = itemPrices.get(itemId);
                                    priceInput.setAttribute('data-vendor-offer-id', voId);
                                    console.log(`Set vendor_offer_id ${voId} for item ${itemId} directly from URL params`);
                                }
                            }
                            // Then try to find in vendor_offers
                            else if (item.vendor_offers && item.vendor_offers.length > 0 && selectedVendorId) {
                                // Check for existing vendor offer
                                const vendorOffer = item.vendor_offers.find(
                                    offer => offer.vendor && parseInt(offer.vendor.vendor_id) === parseInt(selectedVendorId)
                                );

                                if (vendorOffer && vendorOffer.unit_price !== undefined) {
                                    priceInput.value = Number(vendorOffer.unit_price).toLocaleString('id-ID');

                                    // Store the vendor_offer_id for this item
                                    if (vendorOffer.vendor_offer_id) {
                                        priceInput.setAttribute('data-vendor-offer-id', vendorOffer.vendor_offer_id);
                                        console.log(`Set vendor_offer_id ${vendorOffer.vendor_offer_id} for item ${itemId} from vendor offers`);
                                    }
                                }
                            }
                            // Lastly, check if itemPrices is a Map with vendor_offer_ids only (no prices)
                            else if (itemPrices instanceof Map && itemPrices.has(itemId) && typeof itemPrices.get(itemId) === 'number') {
                                const voId = itemPrices.get(itemId);
                                priceInput.setAttribute('data-vendor-offer-id', voId);
                                console.log(`Set vendor_offer_id ${voId} for item ${itemId} from URL params map`);
                            }

                            // Add input mask for currency
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

                        // If we're in edit mode, let's verify all items have vendor_offer_ids
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
                            } else {
                                console.log('Semua item memiliki vendor_offer_id yang ditetapkan dengan benar');
                            }
                        }
                    })
                    .catch(error => {
                        itemsContainer.innerHTML = `<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-red-500">Gagal memuat item: ${error.message}</td></tr>`;
                        showSweetAlert('Gagal memuat data perbandingan: ' + error.message, 'error');
                    });
            }

            // Load all vendors
            function loadAllVendors() {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                vendorResults.style.display = 'block';

                // First try to get from localStorage to avoid delay
                const cachedVendors = localStorage.getItem('allVendors');
                if (cachedVendors) {
                    try {
                        allVendors = JSON.parse(cachedVendors);

                        // Still load fresh data in the background
                        fetchAllVendors();

                        return; // Exit early with cached data
                    } catch (e) {
                        console.error('Error parsing cached vendors:', e);
                    }
                }

                // If no cache, fetch from API
                fetchAllVendors();
            }

            // Fetch all vendors with pagination
            function fetchAllVendors() {
                let page = 1;
                allVendors = []; // Reset array

                function fetchPage(page) {
                    if (page === 1) {
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    } else {
                        // Update loading message for subsequent pages
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor (halaman ' + page + ')...</div>';
                    }

                    fetch(`/vendor?json=true&page=${page}&limit=100`, {
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

                            // Handle different response formats
                            if (Array.isArray(data)) {
                                vendors = data;
                            } else if (data.vendors && Array.isArray(data.vendors)) {
                                vendors = data.vendors;
                                pagination = data.pagination;
                            } else if (data.data && Array.isArray(data.data)) {
                                vendors = data.data;
                                pagination = data.pagination;
                            }

                            // Add to our collection
                            allVendors = [...allVendors, ...vendors];

                            // Check if there are more pages
                            const hasNextPage = pagination && pagination.has_next;

                            if (hasNextPage) {
                                // Fetch next page
                                fetchPage(page + 1);
                            } else {
                                // Cache for future use
                                try {
                                    localStorage.setItem('allVendors', JSON.stringify(allVendors));
                                } catch (e) {
                                    console.error('Error caching vendors:', e);
                                }

                                // If the input has a value, filter and display
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

                            // If we got some vendors, still show them
                            if (allVendors.length > 0) {
                                filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');
                            }
                        });
                }

                // Start fetching from page 1
                fetchPage(page);
            }

            // Filter and display vendors based on search term
            function filterAndDisplayVendors(searchTerm) {
                // Make sure dropdown is visible
                vendorResults.style.display = 'block';

                // Show loading message during search
                if (searchTerm && searchTerm.length > 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Mencari vendor...</div>';
                }

                // If we have no vendors yet
                if (allVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    return;
                }

                // Filter vendors
                let filteredVendors = allVendors;
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    filteredVendors = allVendors.filter(vendor =>
                        vendor.vendor_name?.toLowerCase().includes(term)
                    );
                }

                // Sort by relevance if we have a search term
                if (searchTerm) {
                    filteredVendors.sort((a, b) => {
                        // Exact matches first
                        if (a.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return -1;
                        if (b.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return 1;

                        // Then starts-with matches
                        const aStarts = a.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        const bStarts = b.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        if (aStarts && !bStarts) return -1;
                        if (bStarts && !aStarts) return 1;

                        // Then alphabetical
                        return a.vendor_name.localeCompare(b.vendor_name);
                    });
                }

                // Limit to first 20 for performance
                const displayVendors = filteredVendors.slice(0, 20);

                // Update DOM with animation delay
                vendorResults.innerHTML = '';

                if (displayVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada vendor yang ditemukan</div>';
                    return;
                }

                // Add vendor items with staggered animation
                displayVendors.forEach((vendor, index) => {
                    const div = document.createElement('div');
                    div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                    div.textContent = vendor.vendor_name;
                    div.setAttribute('data-id', vendor.vendor_id);
                    div.style.animationDelay = `${index * 30}ms`; // Staggered animation

                    div.addEventListener('click', function () {
                        vendorIdInput.value = this.getAttribute('data-id');
                        vendorSearchInput.value = this.textContent;
                        vendorResults.style.display = 'none';

                        // If in edit mode and vendor changes, reload items
                        if (isEditMode) {
                            loadComparisonData();
                        }
                    });

                    vendorResults.appendChild(div);
                });

                // Show count if limited
                if (filteredVendors.length > 20) {
                    const countDiv = document.createElement('div');
                    countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                    countDiv.textContent = `Menampilkan 20 dari ${filteredVendors.length} vendor`;
                    vendorResults.appendChild(countDiv);
                }
            }

            // Form submission handler
            if (form) {
                let isSubmitting = false; // Flag to track submission status
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Prevent multiple submissions
                    if (isSubmitting) {
                        return;
                    }

                    // Reset validation errors
                    const allFields = form.querySelectorAll('input, select, textarea');
                    allFields.forEach(field => {
                        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                    });
                    document.querySelectorAll('[id$="_error"]').forEach(el => {
                        el.textContent = '';
                        el.classList.add('hidden');
                    });

                    // Validate vendor selection
                    const vendorId = document.getElementById('selected_vendor_id').value;
                    if (!vendorId) {
                        document.getElementById('vendor_search').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        document.getElementById('vendor_error').textContent = 'Silakan pilih vendor';
                        document.getElementById('vendor_error').classList.remove('hidden');
                        showSweetAlert('Silakan pilih vendor sebelum mengirim.', 'error');
                        return;
                    }

                    // Collect payment and delivery terms
                    const paymentTerms = document.getElementById('payment_terms').value.trim();
                    const deliveryTerms = document.getElementById('delivery_terms').value.trim();

                    // Collect item prices
                    const itemPrices = [];
                    const priceInputs = document.querySelectorAll('table tbody tr input');
                    let hasErroredItem = false;

                    // Determine if we're creating or updating
                    const agreementId = document.getElementById('agreement_id')?.value;
                    const isUpdate = !!agreementId;

                    priceInputs.forEach((input, index) => {
                        const value = input.value.trim().replace(/[^\d]/g, '');

                        // Always get the price_comparison_item_id from data attribute
                        const priceComparisonItemId = input.getAttribute('data-price-comparison-item-id');
                        if (!priceComparisonItemId) {
                            hasErroredItem = true;
                            return;
                        }

                        // If there's a value, process it
                        if (value) {
                            // Get the unit price, making sure to remove formatting
                            const unitPrice = parseInt(value.replace(/[^\d]/g, ''), 10);

                            // Create item data object - include vendor_offer_id if it exists
                            const itemData = {
                                price_comparison_item_id: parseInt(priceComparisonItemId, 10),
                                unit_price: unitPrice
                            };

                            // Add vendor_offer_id if available
                            const vendorOfferId = input.getAttribute('data-vendor-offer-id');
                            if (vendorOfferId) {
                                itemData.vendor_offer_id = parseInt(vendorOfferId, 10);
                                console.log(`Including vendor_offer_id ${vendorOfferId} for item ${priceComparisonItemId} in request`);
                            } else if (isUpdate) {
                                // For update operations, vendor_offer_id is required
                                console.error(`Missing vendor_offer_id for item ${priceComparisonItemId} during update`);
                                input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                hasErroredItem = true;
                            }

                            itemPrices.push(itemData);
                        }
                    });

                    // Don't check for empty prices anymore
                    if (hasErroredItem) {
                        if (isUpdate) {
                            showSweetAlert('Beberapa item tidak memiliki vendor_offer_id. Hal ini diperlukan untuk operasi update. Silakan refresh dan coba lagi.', 'error');
                        } else {
                            showSweetAlert('Beberapa item tidak memiliki data yang diperlukan. Silakan coba lagi.', 'error');
                        }
                        return;
                    }

                    // Get comparison ID from the hidden input field
                    const comparisonId = document.getElementById('comparison_id').value;

                    if (!comparisonId) {
                        showSweetAlert('ID perbandingan tidak ditemukan. Silakan coba lagi atau hubungi dukungan.', 'error');
                        return;
                    }

                    // Prepare request data
                    const requestData = {
                        comparison_id: parseInt(comparisonId, 10),
                        vendor_id: parseInt(vendorId, 10)
                    };

                    // Only add fields that have values
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

                    // Only include items if there are any
                    if (itemPrices.length > 0) {
                        requestData.items = itemPrices;
                    } else {
                        // If no items with prices were provided, create an empty array
                        // This is needed because the backend requires the items field
                        requestData.items = [];
                    }

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    // Set submitting flag and disable button
                    isSubmitting = true;

                    // Show loading state or disable button
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

                    // API endpoint
                    let endpoint = '/procurement/price-comparison/vendor-offer';
                    let method = 'POST';

                    // Use agreement ID for updates if available
                    if (agreementId) {
                        endpoint = `/procurement/price-comparison/vendor-offer/${agreementId}`;
                        method = 'PUT';
                    }

                    // Make the API call
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
                                // Show success alert with automatic redirect
                                showSweetAlert(data.message || 'Penawaran vendor berhasil disimpan!', 'success', {
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        // Set flag to indicate we're navigating away intentionally
                                        isNavigatingAway = true;
                                    },
                                    willClose: () => {
                                        // Redirect after alert closes
                                        window.location.href = data.redirect_url ||
                                            `{{ route('procurement.detail-comparison', ['id' => '_ID_']) }}`.replace('_ID_', comparisonId);
                                    }
                                });
                            } else {
                                // Reset submission flag and button on error
                                isSubmitting = false;
                                submitBtn.innerHTML = originalBtnText;
                                submitBtn.disabled = false;

                                // Handle validation errors
                                if (data.errors) {
                                    let errorMessage = '';

                                    // Check if errors is an array with path and message format
                                    if (Array.isArray(data.errors)) {
                                        errorMessage = '<ul>';

                                        // Process each error in the array
                                        data.errors.forEach(err => {
                                            const fieldPath = err.path || '';
                                            const message = err.message || 'Unknown error';

                                            errorMessage += `<li><strong>${fieldPath}</strong>: ${message}</li>`;

                                            // Highlight the form field if it exists
                                            const fieldElement = document.getElementById(fieldPath);
                                            if (fieldElement) {
                                                fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                            }

                                            // Also show error message below the field
                                            const errorElement = document.getElementById(`${fieldPath}_error`);
                                            if (errorElement) {
                                                errorElement.textContent = message;
                                                errorElement.classList.remove('hidden');
                                            }
                                        });

                                        errorMessage += '</ul>';

                                        // Show error alert with consolidated message
                                        showSweetAlert(errorMessage, 'error');
                                    } else if (typeof data.errors === 'object') {
                                        // Handle object-style errors
                                        Object.entries(data.errors).forEach(([field, messages]) => {
                                            const fieldElement = document.getElementById(field);
                                            if (fieldElement) {
                                                fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                            }

                                            // Display field error messages
                                            const errorElement = document.getElementById(`${field}_error`);
                                            if (errorElement) {
                                                errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                                                errorElement.classList.remove('hidden');
                                            }
                                        });

                                        // Show error alert
                                        showSweetAlert(data.errors?.general || data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                    } else {
                                        // Show error alert with generic or provided message
                                        showSweetAlert(data.errors?.general || data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                    }
                                } else {
                                    // Show error alert
                                    showSweetAlert(data.message || 'Gagal menyimpan penawaran vendor.', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            // Reset flag and button state on error
                            isSubmitting = false;
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;

                            // Check if this is a structured error response
                            if (error.data && error.data.errors) {
                                const errorData = error.data.errors;
                                let errorMessage = '';

                                // Check if errors is an array with path and message format
                                if (Array.isArray(errorData)) {
                                    errorMessage = '<ul>';

                                    // Process each error in the array
                                    errorData.forEach(err => {
                                        const fieldPath = err.path || '';
                                        const message = err.message || 'Unknown error';

                                        errorMessage += `<li><strong>${fieldPath}</strong>: ${message}</li>`;

                                        // Highlight the form field if it exists
                                        const fieldElement = document.getElementById(fieldPath);
                                        if (fieldElement) {
                                            fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                        }

                                        // Also show error message below the field
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

                                        // Highlight the form field if it exists
                                        const fieldElement = document.getElementById(field);
                                        if (fieldElement) {
                                            fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                        }

                                        // Also show error message below the field
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
                                // If we have a plain error message
                                showSweetAlert(`Terjadi kesalahan: ${error.message}`, 'error');
                            } else {
                                // Generic error message
                                showSweetAlert('Terjadi kesalahan saat menyimpan penawaran vendor. Silakan coba lagi.', 'error');
                            }
                        });
                });
            }

            // Add styling for animations
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

                        // Fill vendor data
                        if (vendorOffer.vendor) {
                            document.getElementById('vendor_search').value = vendorOffer.vendor.vendor_name;
                            document.getElementById('selected_vendor_id').value = vendorOffer.vendor.vendor_id;
                        }

                        // Fill form fields
                        document.getElementById('payment_terms').value = vendorOffer.payment_terms || '';
                        document.getElementById('delivery_terms').value = vendorOffer.delivery_terms || '';
                        document.getElementById('notes').value = vendorOffer.notes || '';

                        // Create a map of price comparison item IDs to their unit prices and vendor_offer_id
                        const itemPrices = new Map();

                        // Process items directly from the response
                        if (vendorOffer.items && Array.isArray(vendorOffer.items) && vendorOffer.items.length > 0) {
                            vendorOffer.items.forEach(item => {
                                if (item.price_comparison_item_id && item.unit_price !== undefined) {
                                    // Get vendor_offer_id from the URL params if available
                                    const itemId = parseInt(item.price_comparison_item_id, 10);
                                    const vendorOfferId = vendorOfferIdsMap.has(itemId)
                                        ? vendorOfferIdsMap.get(itemId)
                                        : (item.vendor_offer_id || null);

                                    if (vendorOfferId) {
                                        console.log(`Menggunakan vendor_offer_id ${vendorOfferId} untuk item ${itemId}`);
                                    } else {
                                        console.warn(`Tidak ada vendor_offer_id ditemukan untuk item ${itemId}`);
                                    }

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

                        // After loading the vendor data, load the comparison data
                        loadComparisonData(itemPrices, vendorOffer, parseInt(agreementId, 10));
                    })
                    .catch(error => {
                        showSweetAlert('Gagal memuat data penawaran vendor: ' + error.message, 'error');

                        // Still try to load comparison data even if vendor offer data failed
                        loadComparisonData(vendorOfferIdsMap);
                    });
            }

            // Add event handler for the back button
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
        /* Animation for the vendor search dropdown items */
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