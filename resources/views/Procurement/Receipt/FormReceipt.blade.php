@extends('Layout.app')

@section('title', 'Receipt Form')

@section('content')
<!-- Toast container for notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-4"></div>

<div class="h-full space-y-4 md:space-y-6">
    <!-- Receipt Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">RECEIPT FORM</h1>
                </div>

                <!-- Receipt Form -->
                <form id="receiptForm" class="w-full space-y-6">
                    <!-- Top Row: Receipt Date, Delivered by, Received by -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Receipt Date -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Receipt Date</label>
                            <input type="date" id="receipt_date" name="receipt_date" value="<?php echo date('Y-m-d'); ?>"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        </div>

                        <!-- Delivered by -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Delivered by</label>
                            <div class="flex">
                                <input type="text" id="delivered_by" name="delivered_by"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            </div>
                        </div>

                        <!-- Received by -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Received by</label>
                            <div class="relative">
                                <input type="text" id="receivedByInput" placeholder="Cari penerima..."
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    autocomplete="off">
                                <input type="hidden" id="received_by" name="received_by" value="">

                                <!-- Dropdown for search results -->
                                <div id="users_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                    <!-- Loading indicator -->
                                    <div id="users_loading" class="flex justify-center py-2">
                                        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <ul id="users_list" class="max-h-56 overflow-y-auto"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Section -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">Nomor Purchase Order</label>
                        <div class="relative">
                            <input type="text" id="purchaseOrderNumber" placeholder="Masukkan nomor purchase order"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                autocomplete="off">
                            <input type="hidden" id="selected_po_id" name="purchase_order_id">
                            <input type="hidden" id="notes" name="notes" value="">

                            <div class="absolute inset-y-0 right-0 flex">
                            <button id="searchBtn" type="button" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            </div>

                            <!-- Dropdown for search results -->
                            <div id="po_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                <!-- Loading indicator -->
                                <div id="po_loading" class="flex justify-center py-2">
                                    <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <ul id="po_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Details (Initially Hidden) -->
                    <div id="poDetails" class="border border-[#CCCCCC] rounded-lg p-4 bg-[#F9FAFB] mt-6 hidden">
                        <!-- PO Information -->
                        <div class="grid grid-cols-1 gap-3">
                            <!-- PO Number -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Nomor PO</p>
                                <p class="text-[#666666]">: <span id="displayPoCode"></span></p>
                            </div>

                            <!-- Supplier -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Vendor</p>
                                <p class="text-[#666666]">: <span id="displayVendor"></span></p>
                            </div>

                            <!-- PIC -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">PIC</p>
                                <p class="text-[#666666]">: <span id="displayPic"></span></p>
                            </div>

                            <!-- PIC Contact -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">PIC Contact</p>
                                <p class="text-[#666666]">: <span id="displayPicContact"></span></p>
                            </div>

                            <!-- Input Date -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Tanggal PO</p>
                                <p class="text-[#666666]">: <span id="displayPoDate"></span></p>
                            </div>
                        </div>

                        <!-- ASSET LIST -->
                        <div class="space-y-4 mt-4">
                            <h2 class="text-lg font-semibold text-[#666666]">ASSET LIST</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ASSET NAME</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">SPECIFICATION</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">QTY</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">NOTES</th>
                                        </tr>
                                    </thead>
                                    <tbody id="assetListTableBody">
                                        <!-- Items will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="space-y-2 mt-6">
                            <label class="block text-base font-medium text-[#666666]">Catatan Tambahan</label>
                            <textarea id="notesField" rows="3" class="w-full p-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200" placeholder="Masukkan catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.receipt') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
                            BACK
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            SAVE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('receiptForm');
        const searchBtn = document.getElementById('searchBtn');
        const poDetails = document.getElementById('poDetails');
        const purchaseOrderNumber = document.getElementById('purchaseOrderNumber');
        const selectedPoId = document.getElementById('selected_po_id');
        const poDropdown = document.getElementById('po_dropdown');
        const poList = document.getElementById('po_list');
        const poLoading = document.getElementById('po_loading');

        // User search elements
        const receivedByInput = document.getElementById('receivedByInput');
        const receivedByField = document.getElementById('received_by');
        const usersDropdown = document.getElementById('users_dropdown');
        const usersList = document.getElementById('users_list');
        const usersLoading = document.getElementById('users_loading');

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            // Remove existing notifications with the same type
            const existingNotification = document.getElementById(type === 'success' ? 'successNotification' : 'errorNotification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type === 'success' ? 'successNotification' : 'errorNotification';
            notification.className = `bg-${type === 'success' ? 'green' : 'red'}-100 border-l-4 border-${type === 'success' ? 'green' : 'red'}-500 text-${type === 'success' ? 'green' : 'red'}-700 p-4 rounded shadow-md animate-slide-in-right`;
            notification.setAttribute('role', 'alert');

            // Check if message is an object (for errors)
            if (typeof message === 'object' && message !== null && !Array.isArray(message)) {
                // Format error objects into readable message
                let errorContent = '<div class="font-bold">Error!</div><div class="error-message">';

                if (message.errors) {
                    errorContent += formatErrorObject(message.errors);
                } else {
                    // Try to extract individual properties
                    errorContent += Object.entries(message)
                        .map(([key, value]) => {
                            if (Array.isArray(value)) {
                                return `<div>${key}: ${value.join(', ')}</div>`;
                            } else if (typeof value === 'object' && value !== null) {
                                return `<div>${key}: ${formatErrorObject(value)}</div>`;
                            } else {
                                return `<div>${key}: ${value}</div>`;
                            }
                        })
                        .join('');
                }

                errorContent += '</div>';
                message = errorContent;
            }

            // Set inner HTML based on the type
            if (type === 'success') {
                notification.innerHTML = `
                    <div class="flex items-center">
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
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-grow overflow-auto max-h-60">
                            ${message}
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            }

            // Add to toast container
            document.getElementById('toast-container').appendChild(notification);

            // Auto-hide after 5 seconds
            setTimeout(function() {
                if (document.getElementById(notification.id)) {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(function() {
                        if (document.getElementById(notification.id)) {
                            notification.remove();
                        }
                    }, 500);
                }
            }, 5000);

            return notification;
        }

        // Helper function to format error objects
        function formatErrorObject(errors) {
            if (typeof errors === 'string') {
                return errors;
            }

            if (Array.isArray(errors)) {
                return `<ul class="mt-2 ml-4 list-disc">
                    ${errors.map(err => {
                        if (typeof err === 'object' && err !== null) {
                            if (err.message) {
                                return `<li>${err.message}</li>`;
                            } else {
                                return `<li>${JSON.stringify(err)}</li>`;
                            }
                        } else {
                            return `<li>${err}</li>`;
                        }
                    }).join('')}
                </ul>`;
            }

            return `<ul class="mt-2 ml-4 list-disc">
                ${Object.entries(errors).map(([field, messages]) => {
                    if (Array.isArray(messages)) {
                        return `<li><span class="font-medium">${field}:</span> ${messages.join(', ')}</li>`;
                    } else if (typeof messages === 'object' && messages !== null) {
                        return `<li><span class="font-medium">${field}:</span> ${formatErrorObject(messages)}</li>`;
                    } else {
                        return `<li><span class="font-medium">${field}:</span> ${messages}</li>`;
                    }
                }).join('')}
            </ul>`;
        }

        // Add styling for error messages
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
                .error-message ul li ul {
                    margin-top: 0.25rem;
                    margin-bottom: 0.5rem;
                }
            </style>
        `);

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

        // Toggle dropdown visibility on focus
        purchaseOrderNumber.addEventListener('focus', function() {
            poDropdown.classList.remove('hidden');
            if (poList.children.length === 0) {
                loadPurchaseOrders(''); // Initial load on focus
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!purchaseOrderNumber.contains(e.target) && !poDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                poDropdown.classList.add('hidden');
            }
        });

        // Search input handler with debounce
        const debouncedSearch = debounce(function(e) {
            loadPurchaseOrders(e.target.value);
        }, 300);

        purchaseOrderNumber.addEventListener('input', debouncedSearch);

        // Function to load purchase orders
        async function loadPurchaseOrders(searchTerm) {
            // Show loading indicator
            if (poLoading) poLoading.classList.remove('hidden');
            poList.innerHTML = '';

            try {
                // Fetch purchase order data from API
                const response = await fetch(`/procurement/purchase-order?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil daftar purchase order');
                }

                const result = await response.json();
                let purchaseOrders = result.data || [];

                // Populate dropdown
                poList.innerHTML = '';

                if (purchaseOrders.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-4 py-2 text-gray-500 italic';
                    noResults.textContent = 'Tidak ada purchase order ditemukan';
                    poList.appendChild(noResults);
                } else {
                    purchaseOrders.forEach(po => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        // Format display text - only show PO code
                        const displayText = po.purchase_order_code || '';

                        li.textContent = displayText;
                        li.setAttribute('data-id', po.purchase_order_id);
                        li.setAttribute('data-code', po.purchase_order_code);
                        li.setAttribute('data-vendor', po.vendor_name || '');
                        li.setAttribute('data-user', po.creator_name || 'Staff');
                        li.setAttribute('data-date', po.created_at || '');

                        li.addEventListener('click', function() {
                            // Set the selected PO values
                            selectedPoId.value = this.getAttribute('data-id');
                            purchaseOrderNumber.value = this.getAttribute('data-code');

                            // Hide dropdown
                            poDropdown.classList.add('hidden');
                        });

                        poList.appendChild(li);
                    });
                }
            } catch (error) {
                console.error('Error loading purchase orders:', error);
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = 'Gagal memuat daftar purchase order';
                poList.appendChild(errorItem);
            } finally {
                if (poLoading) poLoading.classList.add('hidden');
            }
        }

        // Add search button click event
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                // Validate PO number
                if (!purchaseOrderNumber.value.trim()) {
                    showToast('Mohon masukkan nomor purchase order', 'error');
                    return;
                }

                // If a selected ID is available, ensure it's a valid number
                if (selectedPoId.value && isNaN(parseInt(selectedPoId.value, 10))) {
                    showToast('ID purchase order tidak valid', 'error');
                    return;
                }

                // Show loading indicator on button
                const originalBtnText = searchBtn.innerHTML;
                searchBtn.disabled = true;
                searchBtn.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                `;

                // Get the PO ID
                const poId = selectedPoId.value || null;
                const poCode = purchaseOrderNumber.value.trim();

                if (poId) {
                    // Fetch PO details using the ID
                    fetchPurchaseOrderDetails(parseInt(poId, 10))
                        .finally(() => {
                            // Reset button state
                            searchBtn.disabled = false;
                            searchBtn.innerHTML = originalBtnText;
                        });
                } else {
                    // Search by code
                    fetch(`/procurement/purchase-order?search=${encodeURIComponent(poCode)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal mencari data purchase order');
                            return response.json();
                        })
                        .then(result => {
                            if (result.success && result.data && result.data.length > 0) {
                                // Find exact match by code
                                const exactMatch = result.data.find(item =>
                                    item.purchase_order_code &&
                                    item.purchase_order_code.toLowerCase() === poCode.toLowerCase());

                                if (exactMatch) {
                                    selectedPoId.value = exactMatch.purchase_order_id;
                                    return fetchPurchaseOrderDetails(parseInt(exactMatch.purchase_order_id, 10));
                                } else {
                                    throw new Error('Nomor purchase order tidak ditemukan, silakan periksa kembali');
                                }
                            } else {
                                throw new Error('Nomor purchase order tidak ditemukan, silakan periksa kembali');
                            }
                        })
                        .catch(error => {
                            console.error('Error searching for purchase order:', error);
                            showToast(error.message || 'Terjadi kesalahan saat mencari data purchase order', 'error');

                            // Hide details section if there was an error
                            poDetails.classList.add('hidden');
                        })
                        .finally(() => {
                            // Reset button state
                            searchBtn.disabled = false;
                            searchBtn.innerHTML = originalBtnText;
                        });
                }
            });
        }

        // Function to fetch purchase order details by ID
        async function fetchPurchaseOrderDetails(poId) {
            try {
                // Clear existing data in case of re-fetch
                document.getElementById('assetListTableBody').innerHTML = '';

                // Fetch the purchase order
                const response = await fetch(`/procurement/detail-purchase-order/${poId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil detail purchase order');
                }

                const result = await response.json();

                if (!result.success || !result.data) {
                    throw new Error(result.errors?.general || 'Data tidak valid dari server');
                }

                const purchaseOrder = result.data;

                console.log("Purchase order data:", purchaseOrder);

                // Update the form with PO details
                document.getElementById('displayPoCode').textContent = purchaseOrder.purchase_order_code || '';

                // Check if vendor is a nested object and extract information
                if (purchaseOrder.vendor && typeof purchaseOrder.vendor === 'object') {
                    document.getElementById('displayVendor').textContent = purchaseOrder.vendor.vendor_name || '';
                    document.getElementById('displayPic').textContent = purchaseOrder.vendor.contact_person || '';
                    document.getElementById('displayPicContact').textContent = purchaseOrder.vendor.phone_number || '';
                } else {
                    document.getElementById('displayVendor').textContent = purchaseOrder.vendor_name || '';
                    document.getElementById('displayPic').textContent = purchaseOrder.creator_name || '';
                    document.getElementById('displayPicContact').textContent = '';
                }

                // Format date properly
                let displayDate = purchaseOrder.created_at || '';
                if (displayDate) {
                    // Try to format the date if possible
                    try {
                        const date = new Date(displayDate);
                        if (!isNaN(date)) {
                            displayDate = date.toISOString().replace('T', ' ').substring(0, 19);
                        }
                    } catch (e) {
                        console.error('Date formatting error:', e);
                    }
                }
                document.getElementById('displayPoDate').textContent = displayDate;

                // Populate asset list table with PO items
                populateAssetList(purchaseOrder.items || []);

                // Show the order details section
                poDetails.classList.remove('hidden');

            } catch (error) {
                console.error('Error fetching purchase order details:', error);
                showToast('Gagal memuat detail purchase order: ' + error.message, 'error');

                // Hide sections on error
                poDetails.classList.add('hidden');
            }
        }

        // Function to populate asset list table with PO items
        function populateAssetList(items) {
            const tableBody = document.getElementById('assetListTableBody');
            tableBody.innerHTML = '';

            if (!items || items.length === 0) {
                // If no items, show a message
                const row = document.createElement('tr');
                row.className = 'border-t border-[#EEF1F4]';

                const cell = document.createElement('td');
                cell.className = 'p-3 text-xs text-[#666666] text-center';
                cell.colSpan = 4;
                cell.textContent = 'Tidak ada item ditemukan untuk purchase order ini';

                row.appendChild(cell);
                tableBody.appendChild(row);
            } else {
                // Add each item as a row
                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = 'border-t border-[#EEF1F4]';

                    // Asset name cell
                    const nameCell = document.createElement('td');
                    nameCell.className = 'p-3 text-xs text-[#666666]';
                    nameCell.textContent = item.procurement_item_name || 'Item ' + (index + 1);
                    row.appendChild(nameCell);

                    // Specification cell
                    const specCell = document.createElement('td');
                    specCell.className = 'p-3 text-xs text-[#666666]';
                    specCell.textContent = item.specification || '-';
                    row.appendChild(specCell);

                    // Quantity cell
                    const qtyCell = document.createElement('td');
                    qtyCell.className = 'p-3 text-xs text-center text-[#666666]';
                    qtyCell.textContent = item.quantity || 1;
                    row.appendChild(qtyCell);

                    // Notes cell with input
                    const notesCell = document.createElement('td');
                    notesCell.className = 'p-3 text-xs text-[#666666]';

                    // Create input for notes
                    const notesInput = document.createElement('input');
                    notesInput.type = 'text';
                    notesInput.placeholder = 'Add notes';
                    notesInput.className = 'w-full p-2 border border-[#CCCCCC] rounded-md text-[#666666]';
                    notesInput.name = `item_notes[${item.purchase_order_item_id}]`;
                    notesInput.dataset.item_id = item.purchase_order_item_id;

                    notesCell.appendChild(notesInput);
                    row.appendChild(notesCell);

                    // Add hidden input for item id
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'items[]';
                    hiddenInput.value = item.purchase_order_item_id;
                    row.appendChild(hiddenInput);

                    tableBody.appendChild(row);
                });
            }
        }

        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                if (!selectedPoId.value) {
                    showToast('Mohon pilih purchase order terlebih dahulu', 'error');
                    return;
                }

                if (!document.getElementById('receipt_date').value) {
                    showToast('Tanggal penerimaan harus diisi', 'error');
                    return;
                }

                if (!document.getElementById('delivered_by').value) {
                    showToast('Nama pengirim harus diisi', 'error');
                    return;
                }

                const receivedById = document.getElementById('received_by').value;
                if (!receivedById) {
                    showToast('Penerima harus dipilih', 'error');
                    console.log('Missing received_by value. Make sure to select a user from the dropdown.');
                    return;
                }

                // Log all form values for debugging
                console.log('Form values before submission:', {
                    purchase_order_id: selectedPoId.value,
                    receipt_date: document.getElementById('receipt_date').value,
                    received_by: receivedById,
                    delivered_by: document.getElementById('delivered_by').value,
                    receivedByName: document.getElementById('receivedByInput').value
                });

                // Get items with notes
                const items = [];
                const itemInputs = document.querySelectorAll('input[name="items[]"]');

                itemInputs.forEach(input => {
                    const item_id = input.value;
                    const notes_input = document.querySelector(`input[data-item_id="${item_id}"]`);

                    items.push({
                        purchase_order_item_id: parseInt(item_id),
                        notes: notes_input ? notes_input.value : ''
                    });
                });

                if (items.length === 0) {
                    showToast('Tidak ada item yang dipilih', 'error');
                    return;
                }

                // Make sure to convert received_by to a number
                const receivedByValue = parseInt(receivedById, 10);
                if (isNaN(receivedByValue)) {
                    showToast('ID penerima tidak valid', 'error');
                    return;
                }

                // Get the notes value from the textarea
                const notesValue = document.getElementById('notesField').value || '';

                // Prepare receipt data
                const receiptData = {
                    purchase_order_id: parseInt(selectedPoId.value),
                    receipt_date: document.getElementById('receipt_date').value,
                    received_by: receivedByValue, // Use the parsed integer value
                    delivered_by: document.getElementById('delivered_by').value,
                    notes: notesValue, // Use the value from the notes textarea
                    items: items
                };

                console.log('Submitting receipt:', receiptData);

                // Submit data to the server
                fetch('{{ route("procurement.receipt.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(receiptData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Penerimaan barang berhasil dibuat!');
                        setTimeout(() => {
                            window.location.href = "{{ route('procurement.receipt') }}";
                        }, 1500);
                    } else {
                        // Enhanced error handling
                        if (data.errors) {
                            showToast({ errors: data.errors }, 'error');
                        } else {
                            showToast(data.message || 'Gagal membuat penerimaan barang', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error creating receipt:', error);
                    showToast('Terjadi kesalahan saat membuat penerimaan barang', 'error');
                });
            });
        }

        // User search functionality
        // Toggle dropdown visibility on focus
        receivedByInput.addEventListener('focus', function() {
            usersDropdown.classList.remove('hidden');
            if (usersList.children.length === 0) {
                loadUsers(''); // Initial load on focus
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!receivedByInput.contains(e.target) && !usersDropdown.contains(e.target)) {
                usersDropdown.classList.add('hidden');
            }
        });

        // Search input handler with debounce
        const debouncedUserSearch = debounce(function(e) {
            loadUsers(e.target.value);
        }, 300);

        receivedByInput.addEventListener('input', debouncedUserSearch);

        // Function to load users
        async function loadUsers(searchTerm) {
            // Show loading indicator
            if (usersLoading) usersLoading.classList.remove('hidden');
            usersList.innerHTML = '';

            try {
                // Fetch users data from API
                const response = await fetch(`{{ route('user.search') }}?query=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil daftar pengguna');
                }

                const result = await response.json();
                let users = result.data || [];

                // Populate dropdown
                usersList.innerHTML = '';

                if (users.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-4 py-2 text-gray-500 italic';
                    noResults.textContent = 'Tidak ada pengguna ditemukan';
                    usersList.appendChild(noResults);
                } else {
                    // Debug output - check what fields are available in the user data
                    console.log('User data structure:', users[0]);

                    users.forEach(user => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        // Try to get user name from various possible fields
                        const userName =  user.employee_name || '';

                        // Format display text - use employee number and name if available
                        let displayText = '';
                        if (user.employee_number) {
                            displayText = user.employee_number;
                            if (userName) {
                                displayText += ` - ${userName}`;
                            }
                        } else {
                            displayText = userName || `ID: ${user.user_id || user.id}`;
                        }

                        li.textContent = displayText;
                        // Use user_id as the value
                        li.setAttribute('data-id', user.user_id || user.id || '');
                        li.setAttribute('data-name', displayText);

                        li.addEventListener('click', function() {
                            // Set the selected user values
                            receivedByField.value = this.getAttribute('data-id');
                            receivedByInput.value = this.getAttribute('data-name');

                            // For debugging
                            console.log('Selected user ID:', this.getAttribute('data-id'));
                            console.log('Selected user name:', this.getAttribute('data-name'));

                            // Hide dropdown
                            usersDropdown.classList.add('hidden');
                        });

                        usersList.appendChild(li);
                    });
                }
            } catch (error) {
                console.error('Error loading users:', error);
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = 'Gagal memuat daftar pengguna';
                usersList.appendChild(errorItem);
            } finally {
                if (usersLoading) usersLoading.classList.add('hidden');
            }
        }

        // Add slide-in animation styling
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
@endpush
