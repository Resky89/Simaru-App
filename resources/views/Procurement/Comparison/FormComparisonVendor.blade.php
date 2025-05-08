@extends('Layout.app')

@section('title', 'Add Vendor Quotation')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Quotation Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                        {{ isset($vendorOffer) ? 'EDIT VENDOR QUOTATION' : 'ADD VENDOR QUOTATION' }}
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
                            {{ isset($vendorOffer) ? 'UPDATE' : 'SUBMIT' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg transform transition-transform duration-300 scale-0 z-50">
    <div class="flex items-center">
        <div id="toast_icon" class="mr-2"></div>
        <div id="toast_message" class="text-sm font-medium"></div>
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

        // If we have a vendor_offer_id, load the vendor offer data
        if (vendorOfferId) {
            loadVendorOfferData(vendorOfferId);
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

                // After loading the vendor data, load the comparison data
                loadComparisonData();
            })
            .catch(error => {
                console.error('Error loading vendor offer data:', error);
                showToast('Failed to load vendor offer data: ' + error.message, 'error');

                // Still try to load comparison data even if vendor offer data failed
                loadComparisonData();
            });
        }

        function showToast(message, type = 'success') {
            // Set message
            toastMessage.textContent = message;

            // Set icon and color based on type
            if (type === 'success') {
                toast.classList.add('bg-green-100', 'text-green-700');
                toastIcon.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            } else {
                toast.classList.add('bg-red-100', 'text-red-700');
                toastIcon.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            }

            // Show toast
            toast.classList.remove('scale-0');
            toast.classList.add('scale-100');

            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.remove('scale-100');
                toast.classList.add('scale-0');

                // Clean up classes after animation
                setTimeout(() => {
                    toast.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                }, 300);
            }, 3000);
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
        function loadComparisonData() {
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

                // Clear container
                itemsContainer.innerHTML = '';

                if (comparisonItems.length === 0) {
                    itemsContainer.innerHTML = '<tr class="border-t border-[#EEF1F4]"><td colspan="3" class="p-3 text-center text-gray-500">No items found</td></tr>';
                    return;
                }

                // Add items to the table
                comparisonItems.forEach((item, index) => {
                    const vendorOffer = isEditMode && item.vendor_offers ?
                        item.vendor_offers.find(offer => offer.vendor?.vendor_id == vendorIdInput.value) : null;

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

                    if (isEditMode) {
                        if (vendorOffer) {
                            // For update, use vendor_offer_id
                            priceInput.setAttribute('data-vendor-offer-id', vendorOffer.vendor_offer_id);
                            priceInput.value = vendorOffer.unit_price;
                        } else {
                            // For items without an offer yet, use price_comparison_item_id
                            priceInput.setAttribute('data-price-comparison-item-id', item.price_comparison_item_id);
                        }
                    } else {
                        // For create, use price_comparison_item_id
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
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate vendor selection
                const vendorId = document.getElementById('selected_vendor_id').value;
                if (!vendorId) {
                    alert('Please select a vendor before submitting.');
                    return;
                }

                // Collect payment and delivery terms
                const paymentTerms = document.querySelector('textarea[placeholder="Payment Terms"]').value.trim();
                if (!paymentTerms) {
                    alert('Please enter payment terms.');
                    return;
                }

                const deliveryTerms = document.querySelector('textarea[placeholder="Delivery Terms"]').value.trim();
                if (!deliveryTerms) {
                    alert('Please enter delivery terms.');
                    return;
                }

                // Collect item prices
                const itemPrices = [];
                const priceInputs = document.querySelectorAll('table tbody tr input');
                let hasEmptyPrice = false;

                priceInputs.forEach((input) => {
                    const value = input.value.trim().replace(/[^\d]/g, '');
                    if (!value) {
                        hasEmptyPrice = true;
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
                        alert('Error: Some items are missing required data. Please reload the page and try again.');
                        return;
                    }

                    itemPrices.push(itemData);
                });

                if (hasEmptyPrice) {
                    alert('Please enter prices for all items.');
                    return;
                }

                // Get comparison ID from the hidden input field - FIX THE ISSUE HERE
                const comparisonId = document.getElementById('comparison_id').value;
                console.log('Using comparison ID:', comparisonId);

                if (!comparisonId) {
                    alert('Error: Comparison ID is missing. Please try again or contact support.');
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

                // Show loading state or disable button
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.textContent;
                submitBtn.textContent = 'Processing...';
                submitBtn.disabled = true;

                // Determine if we're creating or updating
                const isUpdate = window.location.pathname.includes('edit-vendor-offer');
                const vendorOfferId = isUpdate ? urlParams.get('offer_id') : null;

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
                        throw new Error(`Server responded with ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    submitBtn.textContent = originalBtnText;
                    submitBtn.disabled = false;

                    if (data.success) {
                        // Show success message
                        alert(data.message || 'Vendor quotation saved successfully!');

                        // Redirect to the comparison detail page
                        window.location.href = data.redirect_url ||
                            `{{ route('procurement.detail-comparison', ['id' => '_ID_']) }}`.replace('_ID_', comparisonId);
                    } else {
                        // Show error message
                        const errorMsg = data.errors?.general || 'Failed to save vendor quotation.';
                        alert(errorMsg);
                    }
                })
                .catch(error => {
                    // Reset button state
                    submitBtn.textContent = originalBtnText;
                    submitBtn.disabled = false;

                    console.error('Error saving vendor quotation:', error);
                    alert('An error occurred while saving the vendor quotation.');
                });
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
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    /* Toast animation */
    #toast {
        transition: transform 0.3s ease-in-out;
    }
</style>
@endpush
