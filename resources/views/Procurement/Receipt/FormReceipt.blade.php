@extends('Layout.app')

@section('title', 'Receipt Form')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toast container for notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-4"></div>

    <div class="h-full space-y-4 md:space-y-6">
        @if(hasPermission('receipt:create'))
                <!-- Receipt Form Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-4 md:p-7">
                        <div class="flex flex-col gap-6">
                            <!-- Header -->
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex items-center">
                                    <a href="{{ route('procurement.receipt') }}"
                                        class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </a>
                                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORMULIR PENERIMAAN</h1>
                                </div>
                            </div>

                            <!-- Receipt Form -->
                            <form id="receiptForm" class="w-full space-y-6" data-no-loading>
                                <!-- Top Row: Receipt Date, Delivered by, Received by -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Receipt Date -->
                                    <div class="form-control">
                                        <label class="block text-base font-medium text-[#666666] mb-2">Tanggal Penerimaan <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" id="receipt_date" name="receipt_date"
                                            value="<?php    echo date('Y-m-d'); ?>"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal penerimaan harus diisi
                                        </div>
                                    </div>

                                    <!-- Delivered by -->
                                    <div class="form-control">
                                        <label class="block text-base font-medium text-[#666666] mb-2">Dikirim oleh <span
                                                class="text-red-500">*</span></label>
                                        <div class="flex">
                                            <input type="text" id="delivered_by" name="delivered_by" placeholder="Ketik nama"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama pengirim harus diisi</div>
                                    </div>

                                    <!-- Received by -->
                                    <div class="form-control">
                                        <label class="block text-base font-medium text-[#666666] mb-2">Diterima oleh <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="flex">
                                                <input type="text" id="receivedByInput" placeholder="Cari pegawai..."
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                    autocomplete="off">
                                            </div>
                                            <input type="hidden" id="received_by" name="received_by" value="">

                                            <!-- Dropdown for search results -->
                                            <div id="users_dropdown"
                                                class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                                <!-- Loading indicator -->
                                                <div id="users_loading" class="flex justify-center py-2 hidden">
                                                    <svg class="animate-spin h-5 w-5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <ul id="users_list" class="max-h-56 overflow-y-auto"></ul>
                                            </div>
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Penerima harus dipilih dari
                                            daftar pegawai</div>
                                    </div>
                                </div>

                                <!-- Search Section -->
                                <div class="space-y-4">
                                    <label class="block text-base font-semibold text-[#666666]">Nomor Pemesanan</label>
                                    <div class="relative">
                                        <input type="text" id="purchaseOrderNumber" placeholder="Masukkan nomor pemesanan"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            autocomplete="off">
                                        <input type="hidden" id="selected_po_id" name="purchase_order_id">
                                        <input type="hidden" id="notes" name="notes" value="">

                                        <div class="absolute inset-y-0 right-0 flex">
                                            <button id="searchBtn" type="button"
                                                class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Dropdown for search results -->
                                        <div id="po_dropdown"
                                            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                            <!-- Loading indicator -->
                                            <div id="po_loading" class="flex justify-center py-2">
                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
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
                                            <p class="w-40 text-[#666666] font-medium">Nomor Pemesanan</p>
                                            <p class="text-[#666666]">: <span id="displayPoCode"></span></p>
                                        </div>

                                        <!-- Supplier -->
                                        <div class="flex items-start gap-2">
                                            <p class="w-40 text-[#666666] font-medium">Vendor</p>
                                            <p class="text-[#666666]">: <span id="displayVendor"></span></p>
                                        </div>

                                        <!-- PIC -->
                                        <div class="flex items-start gap-2">
                                            <p class="w-40 text-[#666666] font-medium">PIC</p>
                                            <p class="text-[#666666]">: <span id="displayPic"></span></p>
                                        </div>

                                        <!-- PIC Contact -->
                                        <div class="flex items-start gap-2">
                                            <p class="w-40 text-[#666666] font-medium">Kontak PIC</p>
                                            <p class="text-[#666666]">: <span id="displayPicContact"></span></p>
                                        </div>

                                        <!-- Input Date -->
                                        <div class="flex items-start gap-2">
                                            <p class="w-40 text-[#666666] font-medium">Tanggal Pemesanan</p>
                                            <p class="text-[#666666]">: <span id="displayPoDate"></span></p>
                                        </div>
                                    </div>

                                    <!-- ASSET LIST -->
                                    <div class="space-y-4 mt-4">
                                        <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">NAMA
                                                            ASET</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                                            SPESIFIKASI</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">JML
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">CATATAN
                                                        </th>
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
                                        <textarea id="notesField" rows="3"
                                            class="w-full p-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan catatan tambahan (opsional)"></textarea>
                                    </div>
                                </div>

                                <!-- Form Buttons -->
                                <div class="flex gap-4 mt-8">
                                    <button type="submit"
                                        class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                                        SIMPAN
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
        <!-- Permission Denied Message -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                    <p>Maaf, Anda tidak memiliki izin untuk membuat penerimaan baru.</p>
                </div>
                <div class="flex justify-center mt-6">
                    <a href="{{ route('procurement.receipt') }}"
                        class="px-6 py-3 bg-[#213268] text-white rounded-lg hover:bg-[#152451]">
                        Kembali ke Daftar Penerimaan
                    </a>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // Legacy toast function - keeping for backward compatibility but using SweetAlert internally
            function showToast(message, type = 'success') {
                return showSweetAlert(message, type);
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

            // Toggle dropdown visibility on focus
            purchaseOrderNumber.addEventListener('focus', function () {
                poDropdown.classList.remove('hidden');
                if (poList.children.length === 0) {
                    loadPurchaseOrders(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!purchaseOrderNumber.contains(e.target) && !poDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                    poDropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function (e) {
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

                    // Now fetch all existing receipts to check which purchase orders to exclude
                    const receiptsResponse = await fetch('/procurement/receipt?json=true&limit=1000', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!receiptsResponse.ok) {
                        throw new Error('Gagal mengambil data penerimaan');
                    }

                    const receiptsResult = await receiptsResponse.json();

                    // Create a Set of purchase order IDs that already have receipts
                    const purchaseOrdersWithReceipts = new Set();

                    // Get receipts from the response
                    let receipts = [];
                    if (receiptsResult && receiptsResult.success === true && Array.isArray(receiptsResult.data)) {
                        receipts = receiptsResult.data;
                    } else if (receiptsResult && Array.isArray(receiptsResult.receipts)) {
                        receipts = receiptsResult.receipts;
                    }

                    // Extract purchase order IDs that already have receipts
                    if (receipts && receipts.length > 0) {
                        receipts.forEach(receipt => {
                            if (receipt && receipt.purchase_order_id) {
                                purchaseOrdersWithReceipts.add(receipt.purchase_order_id);
                            }
                        });
                    }

                    console.log('Found ' + purchaseOrdersWithReceipts.size + ' purchase orders with existing receipts');

                    // Filter purchase orders to only show those without existing receipts
                    const filteredPurchaseOrders = purchaseOrders.filter(po =>
                        !purchaseOrdersWithReceipts.has(po.purchase_order_id)
                    );

                    // Populate dropdown
                    poList.innerHTML = '';

                    if (filteredPurchaseOrders.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada purchase order yang tersedia untuk penerimaan';
                        poList.appendChild(noResults);
                    } else {
                        filteredPurchaseOrders.forEach(po => {
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

                            li.addEventListener('click', function () {
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
                searchBtn.addEventListener('click', function () {
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
                                        // Now check if this PO already has a receipt
                                        return fetch('/procurement/receipt?json=true&limit=1000', {
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                            .then(receiptsResponse => {
                                                if (!receiptsResponse.ok) {
                                                    throw new Error('Gagal mengambil data penerimaan');
                                                }
                                                return receiptsResponse.json();
                                            })
                                            .then(receiptsResult => {
                                                // Extract purchase order IDs that already have receipts
                                                const purchaseOrdersWithReceipts = new Set();

                                                // Get receipts from the response
                                                let receipts = [];
                                                if (receiptsResult && receiptsResult.success === true && Array.isArray(receiptsResult.data)) {
                                                    receipts = receiptsResult.data;
                                                } else if (receiptsResult && Array.isArray(receiptsResult.receipts)) {
                                                    receipts = receiptsResult.receipts;
                                                }

                                                // Extract purchase order IDs that already have receipts
                                                if (receipts && receipts.length > 0) {
                                                    receipts.forEach(receipt => {
                                                        if (receipt && receipt.purchase_order_id) {
                                                            purchaseOrdersWithReceipts.add(receipt.purchase_order_id);
                                                        }
                                                    });
                                                }

                                                // Check if this PO already has a receipt
                                                if (purchaseOrdersWithReceipts.has(exactMatch.purchase_order_id)) {
                                                    throw new Error('Purchase order ini sudah memiliki penerimaan');
                                                }

                                                // If not, proceed with fetching details
                                                selectedPoId.value = exactMatch.purchase_order_id;
                                                return fetchPurchaseOrderDetails(parseInt(exactMatch.purchase_order_id, 10));
                                            });
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
                                displayDate = formatDateIndonesian(displayDate);
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
                let isSubmitting = false; // Flag to track submission status
                let isNavigatingAway = false; // Flag to track if we're intentionally navigating away
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Prevent multiple submissions
                    if (isSubmitting) {
                        return;
                    }

                    // Validate all required fields
                    let isValid = true;

                    // Validate PO selection
                    if (!selectedPoId.value) {
                        showSweetAlert('Mohon pilih purchase order terlebih dahulu', 'error');
                        return;
                    }

                    // Validate receipt date
                    const receiptDateField = document.getElementById('receipt_date');
                    if (!receiptDateField.value) {
                        receiptDateField.classList.add('border-red-500');
                        const errorElement = receiptDateField.closest('.form-control').querySelector('.error-message');
                        if (errorElement) errorElement.classList.remove('hidden');
                        isValid = false;
                    }

                    // Delivered by is optional
                    const deliveredByField = document.getElementById('delivered_by');

                    // Validate received_by
                    if (!validateReceivedBy()) {
                        receivedByInput.classList.add('border-red-500');
                        const errorElement = receivedByInput.closest('.form-control').querySelector('.error-message');
                        if (errorElement) errorElement.classList.remove('hidden');
                        isValid = false;
                    }

                    // If any validation failed, show an error and stop submission
                    if (!isValid) {
                        showSweetAlert('Mohon lengkapi semua field yang wajib diisi', 'error');
                        return;
                    }

                    // Convert employee_id to number for API compatibility
                    receivedByField.value = parseInt(receivedByField.value, 10);
                    if (isNaN(receivedByField.value)) {
                        showToast('ID pegawai tidak valid', 'error');
                        return;
                    }

                    // Log all form values for debugging
                    console.log('Form values before submission:', {
                        purchase_order_id: selectedPoId.value,
                        receipt_date: document.getElementById('receipt_date').value,
                        received_by: receivedByField.value,
                        delivered_by: document.getElementById('delivered_by').value,
                        receivedByName: document.getElementById('receivedByInput').value
                    });

                    // Get items with notes
                    const items = [];
                    const itemInputs = document.querySelectorAll('input[name="items[]"]');

                    itemInputs.forEach(input => {
                        const item_id = input.value;
                        const notes_input = document.querySelector(`input[data-item_id="${item_id}"]`);

                        // Only include notes if they're not empty
                        const itemData = {
                            purchase_order_item_id: parseInt(item_id)
                        };

                        // Add notes only if they exist and aren't empty
                        const noteValue = notes_input ? notes_input.value.trim() : '';
                        if (noteValue) {
                            itemData.notes = noteValue;
                        }

                        items.push(itemData);
                    });

                    if (items.length === 0) {
                        showToast('Tidak ada item yang dipilih', 'error');
                        return;
                    }

                    // Make sure to convert received_by to a number
                    const receivedByValue = parseInt(receivedByField.value, 10);
                    if (isNaN(receivedByValue)) {
                        showToast('ID penerima tidak valid', 'error');
                        return;
                    }

                    // Prepare receipt data (required fields)
                    const receiptData = {
                        purchase_order_id: parseInt(selectedPoId.value),
                        receipt_date: document.getElementById('receipt_date').value,
                        received_by: receivedByValue // Use the parsed integer value
                    };

                    // Add optional fields only if they have values

                    // Add delivered_by if not empty
                    const deliveredByValue = document.getElementById('delivered_by').value.trim();
                    if (deliveredByValue) {
                        receiptData.delivered_by = deliveredByValue;
                    }

                    // Add notes if not empty
                    const notesValue = document.getElementById('notesField').value.trim();
                    if (notesValue) {
                        receiptData.notes = notesValue;
                    }

                    // Add items
                    receiptData.items = items;

                    console.log('Submitting receipt:', receiptData);

                    // Set submitting flag
                    isSubmitting = true;

                    // Get the submit button and change its appearance
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        MENYIMPAN...
                    `;

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
                                // Show success message with automatic redirect
                                showSweetAlert(
                                    data.message || 'Penerimaan barang berhasil dibuat!',
                                    'success',
                                    {
                                        timer: 1500,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            // Indicate we're navigating away intentionally
                                            isNavigatingAway = true;
                                        },
                                        willClose: () => {
                                            // Redirect after message closes
                                            window.location.href = "{{ route('procurement.receipt') }}";
                                        }
                                    }
                                );
                            } else {
                                // Reset submission state
                                isSubmitting = false;
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                // Enhanced error handling
                                if (data.errors) {
                                    const errorData = data.errors;

                                    // Initialize error message
                                    let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda:';
                                    let errorList = [];

                                    // Process error data
                                    if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                        Object.entries(errorData).forEach(([field, errors]) => {
                                            if (Array.isArray(errors)) {
                                                errors.forEach(err => {
                                                    errorList.push(`${err}`);
                                                });
                                            } else if (typeof errors === 'string') {
                                                errorList.push(`${errors}`);
                                            }
                                        });
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
                                } else {
                                    showSweetAlert(data.message || 'Gagal membuat penerimaan barang', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error creating receipt:', error);

                            // Reset submission state
                            isSubmitting = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;

                            showSweetAlert('Terjadi kesalahan saat membuat penerimaan barang', 'error');
                        });
                });
            }

            // User search functionality

            // Function to validate and show/hide error for user selection
            function validateReceivedBy() {
                const errorElement = receivedByInput.closest('.form-control').querySelector('.error-message');

                if (!receivedByField.value) {
                    if (errorElement) errorElement.classList.remove('hidden');
                    receivedByInput.classList.add('border-red-500');
                    return false;
                } else {
                    if (errorElement) errorElement.classList.add('hidden');
                    receivedByInput.classList.remove('border-red-500');
                    return true;
                }
            }

            // Listen for input changes to clear validation errors
            receivedByInput.addEventListener('input', function () {
                receivedByInput.classList.remove('border-red-500');
                const errorElement = this.closest('.form-control').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            // Clear validation errors when fields change
            document.getElementById('receipt_date').addEventListener('change', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.form-control').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            // Delivered by is optional, no validation needed

            // Toggle dropdown visibility on focus
            receivedByInput.addEventListener('focus', function () {
                usersDropdown.classList.remove('hidden');

                // Show loading message first
                usersList.innerHTML = '<li class="px-4 py-2 text-gray-500 italic">Mulai mengetik untuk mencari pengguna</li>';

                // Load all users on focus
                loadUsers('');
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!receivedByInput.contains(e.target) && !usersDropdown.contains(e.target)) {
                    usersDropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedUserSearch = debounce(function (e) {
                const searchTerm = e.target.value.trim();
                loadUsers(searchTerm);
                usersDropdown.classList.remove('hidden');
            }, 300);

            receivedByInput.addEventListener('input', debouncedUserSearch);

            // Search button has been removed

            // Enable searching when Enter key is pressed in the input field
            receivedByInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const searchTerm = receivedByInput.value.trim();
                    usersDropdown.classList.remove('hidden');
                    loadUsers(searchTerm);
                }
            });

            // Function to load users
            async function loadUsers(searchTerm) {
                // Show loading indicator
                if (usersLoading) {
                    usersLoading.classList.remove('hidden');
                }
                usersList.innerHTML = '';

                try {
                    // Show loading spinner
                    usersLoading.classList.remove('hidden');

                    // Fetch users data using the same endpoint as in Maintenance.blade.php
                    const response = await fetch(`{{ route('user') }}?search=${encodeURIComponent(searchTerm)}&status=active`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil daftar pengguna');
                    }

                    const result = await response.json();
                    // Handle both possible response structures
                    let users = [];
                    if (Array.isArray(result)) {
                        users = result;
                    } else if (result.data && Array.isArray(result.data)) {
                        users = result.data;
                    }

                    // Hide loading spinner
                    usersLoading.classList.add('hidden');

                    // Populate dropdown
                    usersList.innerHTML = '';

                    if (users.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada pengguna ditemukan';
                        usersList.appendChild(noResults);
                    } else {
                        users.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            // Try to get user name from various possible fields
                            const userName = user.employee_name || '';

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

                            li.addEventListener('click', function () {
                                // Set the selected user values
                                receivedByField.value = this.getAttribute('data-id');
                                receivedByInput.value = this.getAttribute('data-name');

                                // Hide dropdown
                                usersDropdown.classList.add('hidden');

                                // Clear any validation errors
                                receivedByInput.classList.remove('border-red-500');
                                const errorElement = receivedByInput.closest('.form-control').querySelector('.error-message');
                                if (errorElement) errorElement.classList.add('hidden');
                            });

                            usersList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    usersList.innerHTML = '';
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Gagal memuat daftar pengguna: ' + (error.message || 'Unknown error');
                    usersList.appendChild(errorItem);
                } finally {
                    // Ensure loading indicator is hidden
                    if (usersLoading) {
                        usersLoading.classList.add('hidden');
                    }
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