@extends('Layout.app')

@section('title', isset($vendorOffer) || request()->has('offer_id') ? 'Edit Vendor Quotation' : 'Add Vendor Quotation')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Quotation Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                        {{ isset($vendorOffer) || request()->has('offer_id') ? 'EDIT VENDOR QUOTATION' : 'ADD VENDOR QUOTATION' }}
                    </h1>
                </div>

                <!-- Form -->
                <form id="vendorQuotationForm" class="w-full space-y-6">
                    @csrf
                    <!-- Hidden Fields -->
                    <input type="hidden" name="comparison_id" id="comparison_id" value="{{ $comparison_id ?? request()->route('id') }}">
                    @if(isset($vendorOffer))
                    <input type="hidden" name="vendor_offer_id" id="vendor_offer_id" value="{{ $vendorOffer->vendor_offer_id }}">
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
                        <label class="block text-base font-semibold text-[#666666]">Payment Terms</label>
                        <textarea
                            name="payment_terms"
                            id="payment_terms"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Payment Terms" rows="3">{{ $vendorOffer->payment_terms ?? '' }}</textarea>
                        <div id="payment_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Delivery Terms -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Delivery Terms</label>
                        <textarea
                            name="delivery_terms"
                            id="delivery_terms"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Delivery Terms" rows="3">{{ $vendorOffer->delivery_terms ?? '' }}</textarea>
                        <div id="delivery_terms_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Notes</label>
                        <textarea
                            name="notes"
                            id="notes"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Additional Notes" rows="2">{{ $vendorOffer->notes ?? '' }}</textarea>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">Asset List</label>

                        <div class="overflow-x-auto">
                            <table class="w-full" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody id="items_container">
                                    <!-- Items will be loaded dynamically -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td colspan="3" class="p-3 text-center text-gray-500">Loading items...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="items_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.detail-comparison', ['id' => $comparison_id ?? request()->route('id')]) }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
                            CANCEL
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            {{ isset($vendorOffer) || request()->has('offer_id') ? 'UPDATE' : 'SUBMIT' }}
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
            <h3 class="mt-4 text-lg font-medium text-gray-900">Success!</h3>
            <p class="mt-2 text-sm text-gray-500" id="successMessage">Your vendor quotation has been successfully saved.</p>
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
            <p class="mt-2 text-sm text-gray-500" id="errorMessage">An error occurred. Please try again.</p>
            <div class="mt-4">
                <button id="errorModalClose" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Close
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

        // Get URL parameters for offer_id if it exists
        const urlParams = new URLSearchParams(window.location.search);
        const offerIdFromUrl = urlParams.get('offer_id');

        // If we have a vendor offer ID from URL, load the vendor offer data
        if (offerIdFromUrl) {
            // Set the vendor_offer_id input
            if (!vendorOfferId) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'vendor_offer_id';
                hiddenInput.id = 'vendor_offer_id';
                hiddenInput.value = offerIdFromUrl;
                form.appendChild(hiddenInput);
            } else {
                document.getElementById('vendor_offer_id').value = offerIdFromUrl;
            }

            // Load vendor offer data
            loadVendorOfferData(offerIdFromUrl);
        } else {
            // If we don't have a vendor_offer_id, load comparison data directly
            loadComparisonData();
        }

        function loadVendorOfferData(vendorOfferId) {
            fetch(`/procurement/price-comparison/vendor-offer/${vendorOfferId}`, {
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
                    throw new Error(data.errors?.general || 'Failed to load vendor offer data');
                }

                const vendorOffer = data.data;
                console.log('Loaded vendor offer data:', vendorOffer);

                // Fill vendor data
                if (vendorOffer.vendor) {
                    document.getElementById('vendor_search').value = vendorOffer.vendor.vendor_name;
                    document.getElementById('selected_vendor_id').value = vendorOffer.vendor.vendor_id;

                    // Disable vendor selection in edit mode
                    document.getElementById('vendor_search').readOnly = true;
                }

                // Fill form fields
                document.getElementById('payment_terms').value = vendorOffer.payment_terms || '';
                document.getElementById('delivery_terms').value = vendorOffer.delivery_terms || '';
                document.getElementById('notes').value = vendorOffer.notes || '';

                // Store vendor offer items for later use
                const vendorOfferItems = [];

                // Parse vendor offer items from the response
                if (vendorOffer.items && Array.isArray(vendorOffer.items) && vendorOffer.items.length > 0) {
                    // Process direct items array (if available)
                    vendorOfferItems.push(...vendorOffer.items);
                    console.log('Found items in vendor offer:', vendorOffer.items);
                }

                if (vendorOffer.vendor_offer_items && Array.isArray(vendorOffer.vendor_offer_items) && vendorOffer.vendor_offer_items.length > 0) {
                    // Process vendor_offer_items array (if available)
                    vendorOfferItems.push(...vendorOffer.vendor_offer_items);
                    console.log('Found vendor_offer_items in vendor offer:', vendorOffer.vendor_offer_items);
                }

                // After loading the vendor data, load the comparison data
                loadComparisonData(vendorOfferItems, vendorOffer);
            })
            .catch(error => {
                console.error('Error loading vendor offer data:', error);
                showToast('Failed to load vendor offer data: ' + error.message, 'error');

                // Still try to load comparison data even if vendor offer data failed
                loadComparisonData([], null);
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
                            <p class="font-bold">Success!</p>
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
                title.textContent = 'Error!';
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
        function loadComparisonData(vendorOfferItems = [], vendorOfferData = null) {
            const itemsContainer = document.getElementById('items_container');
            itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">Loading items...</td></tr>';

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
                    throw new Error(data.errors?.general || 'Failed to load comparison data');
                }

                comparisonItems = data.data.items || [];
                console.log('Loaded comparison items:', comparisonItems);

                // Clear container
                itemsContainer.innerHTML = '';

                if (comparisonItems.length === 0) {
                    itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">No items found</td></tr>';
                    return;
                }

                // Get selected vendor id
                const selectedVendorId = document.getElementById('selected_vendor_id').value;

                // Create a map of price comparison item IDs to their prices for quick lookup
                const priceMap = new Map();

                // First populate from direct vendor offer items (highest priority)
                if (vendorOfferItems && vendorOfferItems.length > 0) {
                    vendorOfferItems.forEach(item => {
                        if (item.price_comparison_item_id && item.unit_price !== undefined) {
                            priceMap.set(
                                parseInt(item.price_comparison_item_id),
                                {
                                    price: item.unit_price,
                                    vendorOfferId: item.vendor_offer_id || item.vendor_offer_item_id
                                }
                            );
                        }
                    });
                }

                // If we have a complete vendor offer, also look for prices in other structures
                if (vendorOfferData && vendorOfferData.price_data) {
                    // Some APIs return a price_data object with item IDs as keys
                    Object.entries(vendorOfferData.price_data).forEach(([itemId, priceData]) => {
                        if (!priceMap.has(parseInt(itemId)) && priceData.unit_price !== undefined) {
                            priceMap.set(
                                parseInt(itemId),
                                {
                                    price: priceData.unit_price,
                                    vendorOfferId: priceData.vendor_offer_id || vendorOfferData.vendor_offer_id
                                }
                            );
                        }
                    });
                }

                // Add items to the table
                comparisonItems.forEach(item => {
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

                    // Check if we have a price for this item in our price map
                    const itemId = parseInt(item.price_comparison_item_id);
                    const priceData = priceMap.get(itemId);

                    if (priceData) {
                        // We found a price in our map - use the vendor_offer_id and set the price
                        priceInput.setAttribute('data-vendor-offer-id', priceData.vendorOfferId);
                        priceInput.value = Number(priceData.price).toLocaleString('id-ID');
                        console.log(`Setting price for item ${itemId} from price map:`, priceData.price);
                    } else if (isEditMode && item.vendor_offers && item.vendor_offers.length > 0 && selectedVendorId) {
                        // Legacy path - look in vendor_offers array if not found in our map
                        const vendorOffer = item.vendor_offers.find(
                            offer => offer.vendor && parseInt(offer.vendor.vendor_id) === parseInt(selectedVendorId)
                        );

                        if (vendorOffer && vendorOffer.unit_price !== undefined) {
                            priceInput.setAttribute('data-vendor-offer-id', vendorOffer.vendor_offer_id);
                            priceInput.value = Number(vendorOffer.unit_price).toLocaleString('id-ID');
                            console.log(`Setting price for item ${itemId} from vendor_offers array:`, vendorOffer.unit_price);
                        } else {
                            // No price found - use price_comparison_item_id for new offers
                            priceInput.setAttribute('data-price-comparison-item-id', item.price_comparison_item_id);
                        }
                    } else {
                        // For items without a price - use price_comparison_item_id
                        priceInput.setAttribute('data-price-comparison-item-id', item.price_comparison_item_id);
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
            })
            .catch(error => {
                console.error('Error loading comparison data:', error);
                itemsContainer.innerHTML = `<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-red-500">Failed to load items: ${error.message}</td></tr>`;
                showToast('Failed to load comparison data', 'error');
            });
        }

        // Load all vendors
        function loadAllVendors() {
            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
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
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                } else {
                    // Update loading message for subsequent pages
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors (page ' + page + ')...</div>';
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
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Error loading vendors</div>';

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
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Searching vendors...</div>';
            }

            // If we have no vendors yet
            if (allVendors.length === 0) {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
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
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">No vendors found</div>';
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
                countDiv.textContent = `Showing 20 of ${filteredVendors.length} vendors`;
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
                    document.getElementById('vendor_error').textContent = 'Please select a vendor';
                    document.getElementById('vendor_error').classList.remove('hidden');
                    showToast('Please select a vendor before submitting.', 'error');
                    return;
                }

                // Collect payment and delivery terms
                const paymentTerms = document.getElementById('payment_terms').value.trim();
                if (!paymentTerms) {
                    document.getElementById('payment_terms').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    document.getElementById('payment_terms_error').textContent = 'Please enter payment terms';
                    document.getElementById('payment_terms_error').classList.remove('hidden');
                    showToast('Please enter payment terms.', 'error');
                    return;
                }

                const deliveryTerms = document.getElementById('delivery_terms').value.trim();
                if (!deliveryTerms) {
                    document.getElementById('delivery_terms').classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    document.getElementById('delivery_terms_error').textContent = 'Please enter delivery terms';
                    document.getElementById('delivery_terms_error').classList.remove('hidden');
                    showToast('Please enter delivery terms.', 'error');
                    return;
                }

                // Collect item prices
                const itemPrices = [];
                const priceInputs = document.querySelectorAll('table tbody tr input');
                let hasEmptyPrice = false;
                let hasErroredItem = false;

                priceInputs.forEach((input) => {
                    const value = input.value.trim().replace(/[^\d]/g, '');
                    if (!value) {
                        input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        hasEmptyPrice = true;
                        hasErroredItem = true;
                        return;
                    }

                    // Get the item ID from data attributes
                    let itemData = {};
                    if (input.hasAttribute('data-price-comparison-item-id')) {
                        // For new offers
                        itemData = {
                            price_comparison_item_id: parseInt(input.getAttribute('data-price-comparison-item-id'), 10),
                            unit_price: parseInt(value, 10)
                        };
                    } else if (input.hasAttribute('data-vendor-offer-id')) {
                        // For updating existing offers
                        itemData = {
                            vendor_offer_id: parseInt(input.getAttribute('data-vendor-offer-id'), 10),
                            unit_price: parseInt(value, 10)
                        };
                    } else {
                        console.error('Item input is missing required data attributes', input);
                        hasErroredItem = true;
                        showToast('Error: Some items are missing required data. Please reload the page and try again.', 'error');
                        return;
                    }

                    itemPrices.push(itemData);
                });

                if (hasEmptyPrice) {
                    showToast('Please enter prices for all items.', 'error');
                    return;
                }

                if (hasErroredItem) {
                    return;
                }

                // Get comparison ID from the hidden input field
                const comparisonId = document.getElementById('comparison_id').value;
                console.log('Using comparison ID:', comparisonId);

                if (!comparisonId) {
                    showToast('Comparison ID is missing. Please try again or contact support.', 'error');
                    console.error('Comparison ID is missing or invalid');
                    return;
                }

                // Prepare request data
                const requestData = {
                    comparison_id: parseInt(comparisonId, 10),
                    vendor_id: parseInt(vendorId, 10),
                    payment_terms: paymentTerms,
                    delivery_terms: deliveryTerms,
                    notes: document.getElementById('notes')?.value.trim() || "Added via vendor quotation form",
                    items: itemPrices
                };

                console.log('Submitting vendor offer with data:', requestData);

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

                // Determine if we're creating or updating
                const vendorOfferId = document.getElementById('vendor_offer_id')?.value;
                const isUpdate = !!vendorOfferId;

                // API endpoint
                const endpoint = isUpdate
                    ? `/procurement/price-comparison/vendor-offer/${vendorOfferId}`
                    : '/procurement/price-comparison/vendor-offer';

                // Make the API call
                fetch(endpoint, {
                    method: isUpdate ? 'PUT' : 'POST',
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
                        showToast(data.message || 'Vendor quotation saved successfully!');

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
                        showToast(data.errors?.general || 'Failed to save vendor quotation.', 'error');

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
                    console.error('Error:', error);

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
                            errorMessage = typeof errorData === 'string' ? errorData : 'An error occurred while saving the vendor quotation.';
                        }

                        showToast(errorMessage, 'error');
                    } else {
                        // Generic error message
                        showToast('An error occurred while saving the vendor quotation. Please try again.', 'error');
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
