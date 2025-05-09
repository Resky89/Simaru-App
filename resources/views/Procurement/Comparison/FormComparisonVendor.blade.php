@extends('Layout.app')

@section('title', isset($vendorOffer) || request()->has('agreement_id') ? 'Edit Penawaran Vendor' : 'Tambah Penawaran Vendor')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Quotation Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.detail-comparison', ['id' => $comparison_id ?? request()->route('id')]) }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                            {{ isset($vendorOffer) || request()->has('agreement_id') ? 'EDIT PENAWARAN VENDOR' : 'TAMBAH PENAWARAN VENDOR' }}
                        </h1>
                    </div>
                </div>

                <!-- Form -->
                <form id="vendorQuotationForm" class="w-full space-y-6">
                    @csrf
                    <!-- Hidden Fields -->
                    <input type="hidden" name="comparison_id" id="comparison_id" value="{{ $comparison_id ?? request()->route('id') }}">
                    <!-- Add a hidden field for agreement_id if it exists -->
                    @if(isset($vendorOffer) && isset($vendorOffer->agreement_id))
                    <input type="hidden" name="agreement_id" id="agreement_id" value="{{ $vendorOffer->agreement_id }}">
                    @endif

                    <!-- Vendor -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Vendor</label>
                        <div class="relative">
                            <input type="text" id="vendor_search"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Search vendor..." autocomplete="off"
                                value="{{ $vendorOffer->vendor->vendor_name ?? '' }}">
                            <input type="hidden" name="vendor_id" id="selected_vendor_id" value="{{ $vendorOffer->vendor->vendor_id ?? '' }}">
                            <div id="vendor_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300 hidden"></div>
                        </div>
                        <div id="vendor_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Payment Terms -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Syarat Pembayaran</label>
                        <textarea
                            name="payment_terms"
                            id="payment_terms"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Payment Terms" rows="3">{{ $vendorOffer->payment_terms ?? '' }}</textarea>
                        <div id="payment_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Delivery Terms -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Syarat Pengiriman</label>
                        <textarea
                            name="delivery_terms"
                            id="delivery_terms"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Delivery Terms" rows="3">{{ $vendorOffer->delivery_terms ?? '' }}</textarea>
                        <div id="delivery_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Catatan</label>
                        <textarea
                            name="notes"
                            id="notes"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Additional Notes" rows="2">{{ $vendorOffer->notes ?? '' }}</textarea>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">Daftar Aset</label>

                        <div class="overflow-x-auto">
                            <table class="w-full" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Jml</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Harga Satuan</th>
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
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            {{ isset($vendorOffer) || request()->has('offer_id') ? 'PERBARUI' : 'KIRIM' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
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
            <p class="mt-2 text-sm text-gray-500" id="successMessage">Penawaran vendor Anda telah berhasil disimpan.</p>
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
            <h3 class="mt-4 text-lg font-medium text-gray-900">Error!</h3>
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
        const form = document.getElementById('vendorQuotationForm');
        const comparisonId = document.getElementById('comparison_id').value;
        const vendorOfferId = document.getElementById('vendor_offer_id')?.value;
        const isEditMode = !!vendorOfferId;
        let comparisonItems = [];

        // Toast notification
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast_message');
        const toastIcon = document.getElementById('toast_icon');

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
                showToast('Gagal memuat data penawaran vendor: ' + error.message, 'error');

                // Still try to load comparison data even if vendor offer data failed
                loadComparisonData(vendorOfferIdsMap);
            });
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
                title.textContent = 'GAGAL!';
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
            return function() {
                const context = this, args = arguments;
                const later = function() {
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
        vendorSearchInput?.addEventListener('focus', function() {
            filterAndDisplayVendors(this.value.trim());
            vendorResults.style.display = 'block';
        });

        // Hide vendor results when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                vendorResults.style.display = 'none';
            }
        });

        // Search vendors with debounce
        vendorSearchInput?.addEventListener('input', debounce(function() {
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
                    priceInput.placeholder = 'Unit Price';

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
                    priceInput.addEventListener('input', function(e) {
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
                        showToast(`Peringatan: ${missingIds.length} item tidak memiliki vendor_offer_id. Hal ini dapat menyebabkan masalah saat menyimpan.`, 'error');
                    } else {
                        console.log('Semua item memiliki vendor_offer_id yang ditetapkan dengan benar');
                    }
                }
            })
            .catch(error => {
                itemsContainer.innerHTML = `<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-red-500">Gagal memuat item: ${error.message}</td></tr>`;
                showToast('Gagal memuat data perbandingan', 'error');
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

                div.addEventListener('click', function() {
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
            form.addEventListener('submit', function(e) {
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
                    showToast('Silakan pilih vendor sebelum mengirim.', 'error');
                    return;
                }

                // Collect payment and delivery terms
                const paymentTerms = document.getElementById('payment_terms').value.trim();
                if (!paymentTerms) {
                    document.getElementById('payment_terms').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    document.getElementById('payment_terms_error').textContent = 'Silakan masukkan syarat pembayaran';
                    document.getElementById('payment_terms_error').classList.remove('hidden');
                    showToast('Silakan masukkan syarat pembayaran.', 'error');
                    return;
                }

                const deliveryTerms = document.getElementById('delivery_terms').value.trim();
                if (!deliveryTerms) {
                    document.getElementById('delivery_terms').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    document.getElementById('delivery_terms_error').textContent = 'Silakan masukkan syarat pengiriman';
                    document.getElementById('delivery_terms_error').classList.remove('hidden');
                    showToast('Silakan masukkan syarat pengiriman.', 'error');
                    return;
                }

                // Collect item prices
                const itemPrices = [];
                const priceInputs = document.querySelectorAll('table tbody tr input');
                let hasEmptyPrice = false;
                let hasErroredItem = false;

                // Determine if we're creating or updating
                const agreementId = document.getElementById('agreement_id')?.value;
                const isUpdate = !!agreementId;

                priceInputs.forEach((input, index) => {
                    const value = input.value.trim().replace(/[^\d]/g, '');
                    if (!value) {
                        input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        hasEmptyPrice = true;
                        hasErroredItem = true;
                        return;
                    }

                    // Always get the price_comparison_item_id from data attribute
                    const priceComparisonItemId = input.getAttribute('data-price-comparison-item-id');
                    if (!priceComparisonItemId) {
                        hasErroredItem = true;
                        return;
                    }

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
                });

                if (hasEmptyPrice) {
                    showToast('Silakan masukkan harga untuk semua item.', 'error');
                    return;
                }

                if (hasErroredItem) {
                    if (isUpdate) {
                        showToast('Beberapa item tidak memiliki vendor_offer_id. Hal ini diperlukan untuk operasi update. Silakan refresh dan coba lagi.', 'error');
                    } else {
                        showToast('Beberapa item tidak memiliki data yang diperlukan. Silakan coba lagi.', 'error');
                    }
                    return;
                }

                // Get comparison ID from the hidden input field
                const comparisonId = document.getElementById('comparison_id').value;

                if (!comparisonId) {
                    showToast('ID perbandingan tidak ditemukan. Silakan coba lagi atau hubungi dukungan.', 'error');
                    return;
                }

                // Prepare request data
                const requestData = {
                    comparison_id: parseInt(comparisonId, 10),
                    vendor_id: parseInt(vendorId, 10),
                    payment_terms: paymentTerms,
                    delivery_terms: deliveryTerms,
                    notes: document.getElementById('notes')?.value.trim(),
                    items: itemPrices
                };

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
                    PROCESSING...
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
                        // Show success toast - don't reset button or submitting flag since we're redirecting
                        showToast(data.message || 'Penawaran vendor berhasil disimpan!');

                        // Set a flag to indicate we're intentionally navigating away
                        const isNavigatingAway = true;

                        // Redirect after success
                        setTimeout(() => {
                            window.location.href = data.redirect_url ||
                                `{{ route('procurement.detail-comparison', ['id' => '_ID_']) }}`.replace('_ID_', comparisonId);
                        }, 1500);
                    } else {
                        // Reset submission flag and button on error
                        isSubmitting = false;
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        // Show error toast
                        showToast(data.errors?.general || data.message || 'Gagal menyimpan penawaran vendor.', 'error');

                        // Handle validation errors
                        if (data.errors && typeof data.errors === 'object') {
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
                        let errorMessage = 'Validation errors occurred:';

                        if (typeof errorData === 'object') {
                            errorMessage += '<ul>';
                            Object.entries(errorData).forEach(([field, messages]) => {
                                const message = Array.isArray(messages) ? messages.join(', ') : messages;
                                errorMessage += `<li><strong>${field}</strong>: ${message}</li>`;

                                // Highlight the form field if it exists
                                const fieldElement = document.getElementById(field);
                                if (fieldElement) {
                                    fieldElement.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                                }
                            });
                            errorMessage += '</ul>';
                        } else {
                            errorMessage = typeof errorData === 'string' ? errorData : 'Terjadi kesalahan saat menyimpan penawaran vendor.';
                        }

                        showToast(errorMessage, 'error');
                    } else if (error.message) {
                        // If we have a plain error message
                        showToast(`Terjadi kesalahan: ${error.message}`, 'error');
                    } else {
                        // Generic error message
                        showToast('Terjadi kesalahan saat menyimpan penawaran vendor. Silakan coba lagi.', 'error');
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
    });
</script>

<style>
    /* Animation for the vendor search dropdown items */
    .vendor-item {
        opacity: 0;
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }
</style>
@endpush
