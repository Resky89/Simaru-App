@extends('Layout.app')

@section('title', 'Form Pemesanan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="h-full space-y-4 md:space-y-6">
    <!-- Purchase Order Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.purchase-order') }}" id="backButton" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORM PEMESANAN</h1>
                    </div>
                </div>

                <!-- Form wrapper with permission check -->
                @if(hasPermission('purchase-order:vendor-offers:select'))
                <!-- Search Section -->
                <div class="space-y-4">
                    <label class="block text-base font-semibold text-[#666666]">Nomor Penawaran</label>
                    <div class="relative">
                        <input type="text" id="quotationNumber" placeholder="Masukkan nomor penawaran"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            autocomplete="off">
                        <input type="hidden" id="selected_comparison_id">

                        <div class="absolute inset-y-0 right-0 flex">
                        <button id="searchBtn" type="button" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                        </div>

                        <!-- Dropdown for search results -->
                        <div id="comparison_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                            <!-- Loading indicator -->
                            <div id="comparison_loading" class="flex justify-center py-2">
                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <ul id="comparison_list" class="max-h-56 overflow-y-auto"></ul>
                        </div>
                    </div>
                </div>

                <!-- Order Details - Hidden by default -->
                <div id="orderDetails" class="grid grid-cols-1 gap-4 hidden">
                    <!-- Nomor -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Nomor Penawaran</p>
                        <p class="text-[#666666]">: <span id="displayComparisonCode">PH2406001</span></p>
                    </div>

                    <!-- Judul -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Judul Penawaran</p>
                        <p class="text-[#666666]">: <span id="displayComparisonTitle">Pembelian Komputer IT</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Pembuat</p>
                        <p class="text-[#666666]">: <span id="displayUserInput">Staff</span></p>
                    </div>

                    <!-- Tanggal Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Tanggal Penawaran</p>
                        <p class="text-[#666666]">: <span id="displayComparisonDate">2024-06-30 06:52:12</span></p>
                    </div>
                </div>

                <!-- Form - Hidden by default -->
                <form id="purchaseOrderForm" class="w-full space-y-6 hidden">
                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">DAFTAR ASET</label>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">NAMA ASET</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">JML</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">PERKIRAAN HARGA</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">PT WIBOWO (PERSERO) TBK</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">PT SETIAWAN</th>
                                    </tr>
                                </thead>
                                <tbody id="assetListTableBody">
                                    <!-- Data will be dynamically loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Catatan</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Tambahkan catatan (opsional)" rows="3" name="notes" id="notes"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <button type="button" id="submitOrderBtn" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            KIRIM
                        </button>
                    </div>
                </form>
                @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                    <p>Maaf, Anda tidak memiliki izin untuk membuat pesanan pembelian.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('purchaseOrderForm');
        const orderDetails = document.getElementById('orderDetails');
        const searchBtn = document.getElementById('searchBtn');
        const quotationNumber = document.getElementById('quotationNumber');
        const selectedComparisonId = document.getElementById('selected_comparison_id');
        const comparisonDropdown = document.getElementById('comparison_dropdown');
        const comparisonList = document.getElementById('comparison_list');
        const comparisonLoading = document.getElementById('comparison_loading');
        let isSubmitting = false; // Flag to track submission status
        let isNavigatingAway = false;
        let formHasBeenFilled = false;

        // No need for JavaScript permission handlers as we're handling permission at the template level

        // Show SweetAlert notifications for session messages on page load
        @if(session('success'))
            showSweetAlert("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showSweetAlert("{{ session('error') }}", 'error');
        @endif

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

        // Replace the toast notification with SweetAlert
        function showToast(message, type = 'success') {
            showSweetAlert(message, type);
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

        // Toggle dropdown visibility on focus
        quotationNumber.addEventListener('focus', function() {
            comparisonDropdown.classList.remove('hidden');
            if (comparisonList.children.length === 0) {
                loadComparisons(''); // Initial load on focus
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!quotationNumber.contains(e.target) && !comparisonDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                comparisonDropdown.classList.add('hidden');
            }
        });

        // Search input handler with debounce
        const debouncedSearch = debounce(function(e) {
            loadComparisons(e.target.value);
        }, 300);

        quotationNumber.addEventListener('input', debouncedSearch);

        // Function to load price comparisons
        async function loadComparisons(searchTerm) {
            // Show loading indicator
            if (comparisonLoading) comparisonLoading.classList.remove('hidden');
            comparisonList.innerHTML = '';

            try {
                // Fetch comparison data from API with proper headers for JSON
                const response = await fetch(`{{ route('procurement.price-comparison-data') }}?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil daftar penawaran');
                }

                const result = await response.json();
                let comparisons = result.data || [];

                // Now fetch all existing purchase orders to check which comparisons to exclude
                const purchaseOrderResponse = await fetch('{{ route("procurement.purchase-order") }}?json=true&limit=1000', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!purchaseOrderResponse.ok) {
                    throw new Error('Gagal mengambil data pemesanan');
                }

                const purchaseOrderResult = await purchaseOrderResponse.json();

                // Create a Set of comparison IDs that already have purchase orders
                const comparisonsWithPurchaseOrders = new Set();

                // Get purchase orders from the response
                let purchaseOrders = [];
                if (purchaseOrderResult && purchaseOrderResult.success === true && Array.isArray(purchaseOrderResult.data)) {
                    purchaseOrders = purchaseOrderResult.data;
                } else if (purchaseOrderResult && Array.isArray(purchaseOrderResult.purchaseOrders)) {
                    purchaseOrders = purchaseOrderResult.purchaseOrders;
                }

                // Extract comparison IDs that already have purchase orders
                if (purchaseOrders && purchaseOrders.length > 0) {
                    purchaseOrders.forEach(po => {
                        if (po && po.comparison_id) {
                            comparisonsWithPurchaseOrders.add(po.comparison_id);
                        }
                    });
                }

                console.log('Found ' + comparisonsWithPurchaseOrders.size + ' comparisons with existing purchase orders');

                // Filter comparisons to only show those without existing purchase orders
                const filteredComparisons = comparisons.filter(comparison =>
                    !comparisonsWithPurchaseOrders.has(comparison.comparison_id)
                );

                // Populate dropdown
                comparisonList.innerHTML = '';

                if (filteredComparisons.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-4 py-2 text-gray-500 italic';
                    noResults.textContent = 'Tidak ada penawaran yang tersedia untuk pemesanan';
                    comparisonList.appendChild(noResults);
                } else {
                    filteredComparisons.forEach(comparison => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        // Format display text - only show comparison code
                        const displayText = comparison.comparison_code || '';

                        li.textContent = displayText;
                        li.setAttribute('data-id', comparison.comparison_id);
                        li.setAttribute('data-code', comparison.comparison_code);
                        li.setAttribute('data-title', comparison.title || '');
                        li.setAttribute('data-user', comparison.user_name || 'Staff');
                        li.setAttribute('data-date', comparison.created_at || '');

                        li.addEventListener('click', function() {
                            // Set the selected comparison values
                            selectedComparisonId.value = this.getAttribute('data-id');
                            quotationNumber.value = this.getAttribute('data-code');

                            // Hide dropdown
                            comparisonDropdown.classList.add('hidden');
                        });

                        comparisonList.appendChild(li);
                    });
                }
            } catch (error) {
                console.error('Error loading price comparisons:', error);
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = 'Gagal memuat daftar penawaran';
                comparisonList.appendChild(errorItem);
            } finally {
                if (comparisonLoading) comparisonLoading.classList.add('hidden');
            }
        }

        // Add search button click event
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                // Validate quotation number
                if (!quotationNumber.value.trim()) {
                    showSweetAlert('Mohon masukkan nomor penawaran', 'error');
                    return;
                }

                // If a selected ID is available, ensure it's a valid number
                if (selectedComparisonId.value && isNaN(parseInt(selectedComparisonId.value, 10))) {
                    showSweetAlert('ID penawaran tidak valid', 'error');
                    return;
                }

                // Show loading indicator on button
                const originalBtnText = searchBtn.innerHTML;
                searchBtn.disabled = true;
                searchBtn.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                `;

                // Get the comparison ID
                const comparisonId = selectedComparisonId.value || null;
                const comparisonCode = quotationNumber.value.trim();

                if (comparisonId) {
                    // Fetch comparison details using the ID
                    fetchComparisonDetails(parseInt(comparisonId, 10))
                        .then(() => {
                            // Track that the form has data loaded
                            formHasBeenFilled = true;
                        })
                        .finally(() => {
                            // Reset button state
                            searchBtn.disabled = false;
                            searchBtn.innerHTML = originalBtnText;
                        });
                } else {
                    // Search by code with proper headers for JSON
                    fetch(`{{ route('procurement.price-comparison-data') }}?search=${encodeURIComponent(comparisonCode)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal mencari data penawaran');
                            return response.json();
                        })
                        .then(result => {
                            if (result.success && result.data && result.data.length > 0) {
                                // Get all price comparisons
                                let comparisons = result.data;

                                // Fetch purchase orders to check which comparisons to exclude
                                return fetch('{{ route("procurement.purchase-order") }}?json=true&limit=1000', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(poResponse => {
                                    if (!poResponse.ok) {
                                        throw new Error('Gagal mengambil data pemesanan');
                                    }
                                    return poResponse.json();
                                })
                                .then(poResult => {
                                    // Create a Set of comparison IDs that already have purchase orders
                                    const comparisonsWithPurchaseOrders = new Set();

                                    // Get purchase orders from the response
                                    let purchaseOrders = [];
                                    if (poResult && poResult.success === true && Array.isArray(poResult.data)) {
                                        purchaseOrders = poResult.data;
                                    } else if (poResult && Array.isArray(poResult.purchaseOrders)) {
                                        purchaseOrders = poResult.purchaseOrders;
                                    }

                                    // Extract comparison IDs that already have purchase orders
                                    if (purchaseOrders && purchaseOrders.length > 0) {
                                        purchaseOrders.forEach(po => {
                                            if (po && po.comparison_id) {
                                                comparisonsWithPurchaseOrders.add(po.comparison_id);
                                            }
                                        });
                                    }

                                    console.log('Found ' + comparisonsWithPurchaseOrders.size + ' comparisons with existing purchase orders');

                                    // Filter comparisons to only show those without existing purchase orders
                                    const filteredComparisons = comparisons.filter(comparison =>
                                        !comparisonsWithPurchaseOrders.has(comparison.comparison_id)
                                    );

                                    if (filteredComparisons.length === 0) {
                                        throw new Error('Tidak ada penawaran yang tersedia untuk pemesanan atau nomor penawaran sudah memiliki pemesanan');
                                    }

                                // Find exact match by code
                                    const exactMatch = filteredComparisons.find(item =>
                                    item.comparison_code &&
                                    item.comparison_code.toLowerCase() === comparisonCode.toLowerCase());

                                if (exactMatch) {
                                    selectedComparisonId.value = exactMatch.comparison_id;
                                    return fetchComparisonDetails(parseInt(exactMatch.comparison_id, 10));
                                } else {
                                    throw new Error('Nomor penawaran tidak ditemukan, silakan periksa kembali nomor penawaran');
                                }
                                });
                            } else {
                                throw new Error('Nomor penawaran tidak ditemukan, silakan periksa kembali nomor penawaran');
                            }
                        })
                        .catch(error => {
                            console.error('Error searching for comparison:', error);
                            showSweetAlert(error.message || 'Terjadi kesalahan saat mencari data penawaran', 'error');

                            // Hide details section if there was an error
                            orderDetails.classList.add('hidden');
                            form.classList.add('hidden');
                        })
                        .finally(() => {
                            // Reset button state
                            searchBtn.disabled = false;
                            searchBtn.innerHTML = originalBtnText;
                        });
                }
            });
        }

        // Function to fetch comparison details by ID
        async function fetchComparisonDetails(comparisonId) {
            try {
                // Clear existing data in case of re-fetch
                document.getElementById('assetListTableBody').innerHTML = '';

                // Fetch the comparison with proper headers for JSON
                const response = await fetch(`{{ url('procurement/price-comparison') }}/${comparisonId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil detail penawaran');
                }

                const result = await response.json();

                if (!result.success || !result.data) {
                    throw new Error(result.errors?.general || 'Data tidak valid dari server');
                }

                const comparison = result.data;

                console.log("Comparison data:", comparison);

                // Store vendors globally to be used in form submit
                window.vendors = comparison.vendors || [];

                // Update the form with comparison details
                document.getElementById('displayComparisonCode').textContent = comparison.comparison_code || '';
                document.getElementById('displayComparisonTitle').textContent = comparison.title || '';
                document.getElementById('displayUserInput').textContent = comparison.created_by?.name || 'Staff';

                // Format date properly
                let displayDate = comparison.created_at || '';
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
                document.getElementById('displayComparisonDate').textContent = displayDate;

                // Extract all unique vendors from the items' vendor offers
                const uniqueVendors = {};
                if (comparison.items && Array.isArray(comparison.items)) {
                    comparison.items.forEach(item => {
                        if (item.vendor_offers && Array.isArray(item.vendor_offers)) {
                            item.vendor_offers.forEach(offer => {
                                if (offer.vendor && offer.vendor.vendor_id) {
                                    const vendorId = offer.vendor.vendor_id;
                                    if (!uniqueVendors[vendorId]) {
                                        uniqueVendors[vendorId] = offer.vendor;
                                        // Add payment_terms and delivery_terms to vendor object
                                        uniqueVendors[vendorId].payment_terms = offer.agreement?.payment_terms || offer.payment_terms || '';
                                        uniqueVendors[vendorId].delivery_terms = offer.agreement?.delivery_terms || offer.delivery_terms || '';
                                    }
                                }
                            });
                        }
                    });
                }

                // Populate asset list table with comparison items and vendor options
                populateAssetList(comparison.items || [], Object.values(uniqueVendors));

                // Show the order details and form sections
                orderDetails.classList.remove('hidden');
                form.classList.remove('hidden');

            } catch (error) {
                console.error('Error fetching comparison details:', error);
                showSweetAlert('Gagal memuat detail penawaran: ' + error.message, 'error');

                // Hide sections on error
                orderDetails.classList.add('hidden');
                form.classList.add('hidden');
            }
        }

        // Function to populate asset list table with comparison items and vendor options
        function populateAssetList(items, vendors) {
            // Get the table container div
            const tableContainer = document.querySelector('.overflow-x-auto');
            tableContainer.innerHTML = '';

            // Create a new table
            const table = document.createElement('table');
            table.className = 'w-full';

            // Create table header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');

            // Add basic headers
            const headers = [
                { text: 'NAMA ASET', align: 'left' },
                { text: 'JML', align: 'center' },
                { text: 'PERKIRAAN HARGA', align: 'left' }
            ];

            // Add vendor headers
            if (vendors && vendors.length > 0) {
                vendors.forEach(vendor => {
                    headers.push({ text: vendor.vendor_name || 'Vendor', align: 'left' });
                });
            }

            // Create header cells
            headers.forEach(header => {
                const th = document.createElement('th');
                th.className = `bg-[#213268] text-white p-3 font-bold text-sm text-${header.align}`;
                th.textContent = header.text;
                headerRow.appendChild(th);
            });

            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');
            tbody.id = 'assetListTableBody';

            console.log("Items:", items);
            console.log("Vendors:", vendors);

            if (!items || items.length === 0) {
                // If no items, show a message
                const row = document.createElement('tr');
                row.className = 'border-t border-[#EEF1F4]';

                const cell = document.createElement('td');
                cell.className = 'p-3 text-sm text-[#666666] text-center';
                cell.colSpan = 3 + (vendors.length || 0);
                cell.textContent = 'Tidak ada item ditemukan untuk penawaran ini';

                row.appendChild(cell);
                tbody.appendChild(row);

                // Add payment and delivery terms rows
                addPaymentAndDeliveryRows(tbody, []);
            } else {
                // Add each item as a row
                items.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = 'border-t border-[#EEF1F4]';

                    // Asset name cell
                    const nameCell = document.createElement('td');
                    nameCell.className = 'p-3 text-sm text-[#666666]';
                    nameCell.textContent = item.procurement_item_name || 'Item ' + (index + 1);
                    row.appendChild(nameCell);

                    // Quantity cell
                    const qtyCell = document.createElement('td');
                    qtyCell.className = 'p-3 text-sm text-center text-[#666666]';
                    qtyCell.textContent = item.quantity || 1;
                    row.appendChild(qtyCell);

                    // Estimated price cell
                    const estPriceCell = document.createElement('td');
                    estPriceCell.className = 'p-3 text-sm text-[#666666]';

                    // Get unit price from the API response
                    const estUnitPrice = parseFloat(item.estimated_unit_price || 0);
                    // Calculate total price from unit price and quantity
                    const estPrice = estUnitPrice * parseInt(item.quantity);

                    // Create price display container
                    const estPriceDiv = document.createElement('div');
                    estPriceDiv.className = 'text-sm font-medium';
                    estPriceDiv.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(estPrice);

                    // Create unit price display container
                    const estUnitPriceSpan = document.createElement('span');
                    estUnitPriceSpan.className = 'text-xs text-gray-500 block mt-1';
                    estUnitPriceSpan.textContent = '@Rp ' + new Intl.NumberFormat('id-ID').format(estUnitPrice);

                    estPriceCell.appendChild(estPriceDiv);
                    estPriceCell.appendChild(estUnitPriceSpan);
                    row.appendChild(estPriceCell);

                    // Add vendor cells with radio buttons for selection
                    if (vendors && vendors.length > 0) {
                        vendors.forEach(vendor => {
                            const vendorCell = document.createElement('td');
                            vendorCell.className = 'p-3';

                            // Find vendor offer for this item from this vendor
                            let vendorOffer = null;
                            if (item.vendor_offers && Array.isArray(item.vendor_offers)) {
                                vendorOffer = item.vendor_offers.find(offer =>
                                    offer.vendor && offer.vendor.vendor_id == vendor.vendor_id
                                );
                            }

                            if (vendorOffer) {
                                // Create price display container
                                const priceContainer = document.createElement('div');

                                // Calculate total price from unit price × quantity
                                const unitPrice = parseFloat(vendorOffer.unit_price || 0);
                                const price = unitPrice * parseInt(item.quantity || 1);

                                // Format total price
                                const priceDiv = document.createElement('div');
                                priceDiv.className = 'text-sm font-medium text-[#666666]';
                                priceDiv.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

                                // Format unit price
                                const unitPriceSpan = document.createElement('span');
                                unitPriceSpan.className = 'text-xs text-gray-500 block mt-1';
                                unitPriceSpan.textContent = '@Rp ' + new Intl.NumberFormat('id-ID').format(unitPrice);

                                priceContainer.appendChild(priceDiv);
                                priceContainer.appendChild(unitPriceSpan);

                                // Create vendor price container with radio button
                                const vendorContainer = document.createElement('div');
                                vendorContainer.className = 'flex items-center';

                                // Create radio input for selecting this vendor for this item
                                const radioInput = document.createElement('input');
                                radioInput.type = 'radio';
                                radioInput.name = `vendor_item${item.price_comparison_item_id}`;
                                radioInput.value = vendor.vendor_id;
                                radioInput.className = 'mr-2';
                                // Set the precise data attributes needed for submission
                                radioInput.dataset.item_id = item.price_comparison_item_id;
                                radioInput.dataset.vendor_id = vendor.vendor_id;
                                radioInput.dataset.vendor_offer_id = vendorOffer.vendor_offer_id;

                                vendorContainer.appendChild(radioInput);
                                vendorContainer.appendChild(priceContainer);

                                vendorCell.appendChild(vendorContainer);
                            } else {
                                // No offer from this vendor
                                vendorCell.textContent = 'Tidak tersedia';
                                vendorCell.className += ' text-xs text-gray-500';
                            }

                            row.appendChild(vendorCell);
                        });
                    }

                    tbody.appendChild(row);
                });

                // Add payment and delivery terms rows
                addPaymentAndDeliveryRows(tbody, vendors);
            }

            table.appendChild(tbody);
            tableContainer.appendChild(table);

            // Add event listeners to track form changes
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            radioInputs.forEach(input => {
                input.addEventListener('change', () => {
                    formHasBeenFilled = true;
                });
            });

            // Track notes field changes
            const notesField = document.getElementById('notes');
            if (notesField) {
                notesField.addEventListener('input', () => {
                    formHasBeenFilled = true;
                });
            }
        }

        // Helper function to add payment and delivery terms rows
        function addPaymentAndDeliveryRows(tbody, vendors) {
            // Payment Terms row
            const paymentRow = document.createElement('tr');
            paymentRow.className = 'border-t border-[#EEF1F4] bg-[#E9ECF6]';

            const paymentLabelCell = document.createElement('td');
            paymentLabelCell.className = 'p-3 text-sm font-medium text-[#213268]';
            paymentLabelCell.textContent = 'Syarat Pembayaran';
            paymentRow.appendChild(paymentLabelCell);

            // Empty cells for spacing
            const paymentEmptyCell = document.createElement('td');
            paymentEmptyCell.colSpan = 2;
            paymentEmptyCell.className = 'p-3';
            paymentRow.appendChild(paymentEmptyCell);

            // Add vendor payment terms if available
            if (vendors && vendors.length > 0) {
                vendors.forEach(vendor => {
                    const vendorPaymentCell = document.createElement('td');
                    vendorPaymentCell.className = 'p-3 text-sm text-[#666666]';
                    vendorPaymentCell.textContent = vendor.payment_terms || '-';
                    paymentRow.appendChild(vendorPaymentCell);
                });
            } else {
                const noVendorPaymentCell = document.createElement('td');
                noVendorPaymentCell.colSpan = 2;
                noVendorPaymentCell.className = 'p-3 text-sm text-gray-500 text-center';
                noVendorPaymentCell.textContent = '-';
                paymentRow.appendChild(noVendorPaymentCell);
            }

            tbody.appendChild(paymentRow);

            // Delivery Terms row
            const deliveryRow = document.createElement('tr');
            deliveryRow.className = 'border-t border-[#EEF1F4] bg-[#E9ECF6]';

            const deliveryLabelCell = document.createElement('td');
            deliveryLabelCell.className = 'p-3 text-sm font-medium text-[#213268]';
            deliveryLabelCell.textContent = 'Syarat Pengiriman';
            deliveryRow.appendChild(deliveryLabelCell);

            // Empty cells for spacing
            const deliveryEmptyCell = document.createElement('td');
            deliveryEmptyCell.colSpan = 2;
            deliveryEmptyCell.className = 'p-3';
            deliveryRow.appendChild(deliveryEmptyCell);

            // Add vendor delivery terms if available
            if (vendors && vendors.length > 0) {
                vendors.forEach(vendor => {
                    const vendorDeliveryCell = document.createElement('td');
                    vendorDeliveryCell.className = 'p-3 text-sm text-[#666666]';
                    vendorDeliveryCell.textContent = vendor.delivery_terms || '-';
                    deliveryRow.appendChild(vendorDeliveryCell);
                });
            } else {
                const noVendorDeliveryCell = document.createElement('td');
                noVendorDeliveryCell.colSpan = 2;
                noVendorDeliveryCell.className = 'p-3 text-sm text-gray-500 text-center';
                noVendorDeliveryCell.textContent = '-';
                deliveryRow.appendChild(noVendorDeliveryCell);
            }

            tbody.appendChild(deliveryRow);
        }

        // Form submission handler
        if (form) {
            document.getElementById('submitOrderBtn').addEventListener('click', function(e) {
                // Prevent multiple submissions
                if (isSubmitting) {
                    return;
                }

                // Store selected items and vendor info
                const selectedItems = [];
                const selectedVendors = new Set();

                // Get all radio inputs for vendor selection
                const radioInputs = document.querySelectorAll('input[type="radio"]:checked');

                if (radioInputs.length === 0) {
                    showSweetAlert('Mohon pilih vendor untuk setidaknya satu item', 'error');
                    return;
                }

                // Process selected vendors and items
                radioInputs.forEach(radio => {
                    const itemId = radio.dataset.item_id;
                    const vendorOfferId = radio.dataset.vendor_offer_id;

                    selectedItems.push({
                        price_comparison_item_id: parseInt(itemId),
                        vendor_offer_id: parseInt(vendorOfferId)
                    });

                    selectedVendors.add(radio.dataset.vendor_id);
                });

                if (selectedItems.length === 0) {
                    showSweetAlert('Tidak ada item yang dipilih', 'error');
                    return;
                }

                // Get form values
                const notes = document.getElementById('notes').value;

                // Get payment and delivery terms from the selected vendor
                let paymentTerms = '';
                let deliveryTerms = '';

                // Use terms from the first selected vendor
                if (selectedVendors.size > 0) {
                    const firstVendorId = Array.from(selectedVendors)[0];
                    const selectedVendor = window.vendors.find(v => v.vendor_id == firstVendorId);

                    if (selectedVendor) {
                        paymentTerms = selectedVendor.payment_terms || '';
                        deliveryTerms = selectedVendor.delivery_terms || '';
                    }
                }

                // Prepare data for submission - exactly match the required format
                const purchaseOrderData = {
                    comparison_id: parseInt(selectedComparisonId.value),
                    selections: selectedItems,
                    notes: notes
                };

                console.log('Submitting purchase order:', purchaseOrderData);

                // Set submission flag and disable submit button
                isSubmitting = true;
                const submitButton = document.getElementById('submitOrderBtn');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    PROSES...
                `;

                // Submit data to the server
                fetch('{{ route("procurement.purchase-order.create-from-vendor-offers") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(purchaseOrderData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert(data.message || 'Pesanan Pembelian berhasil dibuat!', 'success', {
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            didOpen: () => {
                                // Set flag to indicate we're navigating away intentionally
                                isNavigatingAway = true;
                            },
                            willClose: () => {
                                // Redirect after alert closes
                            window.location.href = "{{ route('procurement.purchase-order') }}";
                            }
                        });
                    } else {
                        // Reset submission status if failed
                        isSubmitting = false;
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitButton.innerHTML = originalButtonText;

                        let errorMessage = data.errors || 'Gagal membuat Pesanan Pembelian';
                        if (typeof errorMessage === 'object') {
                            // Convert object to list format
                            let errorList = '<ul class="mt-2 list-disc pl-5">';
                            Object.entries(errorMessage).forEach(([field, message]) => {
                                errorList += `<li>${field}: ${message}</li>`;
                            });
                            errorList += '</ul>';
                            errorMessage = 'Terjadi kesalahan saat memproses pesanan:' + errorList;
                        }

                        showSweetAlert(errorMessage, 'error', {
                            title: 'Gagal Memproses Pesanan',
                            showCloseButton: true,
                            confirmButtonText: 'Coba Lagi'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error creating purchase order:', error);
                    // Reset submission status on error
                    isSubmitting = false;
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitButton.innerHTML = originalButtonText;
                    showSweetAlert('Terjadi kesalahan saat membuat Pesanan Pembelian', 'error', {
                        title: 'Gagal Terhubung ke Server',
                        showCloseButton: true,
                        footer: 'Harap periksa koneksi internet Anda dan coba lagi'
                    });
                });
            });
        }

        // Function to check if form has changes
        function formHasChanges() {
            // Check if form has been explicitly marked as having changes
            if (formHasBeenFilled) return true;

            // Check if comparison ID or quotation number is filled
            if (document.getElementById('selected_comparison_id').value ||
                document.getElementById('quotationNumber').value.trim()) {
                return true;
            }

            // Check for notes field
            const notes = document.getElementById('notes');
            if (notes && notes.value.trim()) {
                return true;
            }

            // Check if any items have been selected with radio buttons
            const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
            if (selectedRadios.length > 0) {
                return true;
            }

            return false;
        }

        // Improve navigation handling with SweetAlert for internal links
        document.addEventListener('click', function(e) {
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

        // Add event handler for the back button - keep this specific handling
        document.getElementById('backButton').addEventListener('click', function(e) {
            if (formHasChanges()) {
                e.preventDefault();
                showSweetAlert(
                    'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?',
                    'warning',
                    {
                        title: 'Perubahan Belum Disimpan',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tinggalkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#213268',
                        cancelButtonColor: '#d33'
                    }
                ).then((result) => {
                    if (result.isConfirmed) {
                        isNavigatingAway = true;
                        window.location.href = '{{ route("procurement.purchase-order") }}';
                    }
                });
            } else {
                isNavigatingAway = true;
            }
        });

        // Keep a limited beforeunload for cases like tab closing, refreshing or external navigation
        // This cannot use SweetAlert due to browser security restrictions
        window.addEventListener('beforeunload', function(e) {
            if (!isNavigatingAway && formHasChanges()) {
                // Modern browsers will show a generic message regardless of what we set here
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

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
