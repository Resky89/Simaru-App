@extends('Layout.app')

@section('title', 'Form Pemesanan')

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
                        <button id="searchBtn" type="button"
                            class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown for search results -->
                    <div id="comparison_dropdown"
                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                        <!-- Loading indicator -->
                        <div id="comparison_loading" class="p-2 text-gray-500 text-center">
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memuat Penawaran...</span>
                        </div>
                        <ul id="comparison_list" class="py-1"></ul>
                        <!-- Load more indicator -->
                        <div id="comparison_load_more" class="p-2 text-gray-500 text-center hidden">
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memuat lebih banyak...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details - Hidden by default -->
            <div id="orderDetails" class="hidden mt-6">
                <div class="border border-[#CCCCCC] rounded-lg p-4 bg-[#F9FAFB]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column - Comparison Information -->
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-[#666666]">Informasi Perbandingan</h2>
                            <table class="w-full">
                                <tbody>
                                    <!-- Nomor Penawaran -->
                                    <tr>
                                        <td class="py-1 align-top w-48 font-medium text-[#666666]">Nomor Penawaran</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayComparisonCode"></span>
                                        </td>
                                    </tr>
                                    <!-- Judul Penawaran -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Judul Penawaran</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayComparisonTitle"></span>
                                        </td>
                                    </tr>
                                    <!-- Tanggal Penawaran -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Tanggal Penawaran</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayComparisonDate"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Right Column - Personnel & Status Information -->
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-[#666666]">Informasi Personil</h2>
                            <table class="w-full">
                                <tbody>
                                    <!-- Pembuat -->
                                    <tr>
                                        <td class="py-1 align-top w-48 font-medium text-[#666666]">Dibuat oleh</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayUserInput"></span></td>
                                    </tr>
                                    <!-- Completer (if available) -->
                                    <tr id="completerSection" style="display: none;">
                                        <td class="py-1 align-top font-medium text-[#666666]">Diselesaikan oleh</td>
                                        <td class="py-1 align-top text-[#666666]">:
                                            <span id="displayCompleterInput"></span>
                                            <span class="text-xs text-gray-500 ml-2" id="displayCompletedDate"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4 mt-6">
                        <h2 class="text-lg font-semibold text-[#666666]">Perbandingan Harga</h2>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Nama Aset</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">Jumlah</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Perkiraan Harga</th>
                                        <!-- Vendor columns will be added dynamically -->
                                    </tr>
                                </thead>
                                <tbody id="assetListTableBody">
                                    <!-- Data will be dynamically loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2 mt-6">
                        <label class="block text-base font-semibold text-[#666666]">Catatan</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Tambahkan catatan (opsional)" rows="3" name="notes" id="notes"></textarea>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 mt-8">
                    <button type="button" id="submitOrderBtn"
                        class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                        KIRIM
                    </button>
                </div>
            </div>

            <!-- Form - Hidden by default -->
            <form id="purchaseOrderForm" class="w-full space-y-6 hidden mt-6" data-no-loading>

            </form>
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
                <p>Maaf, Anda tidak memiliki izin untuk membuat pesanan pembelian.</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('purchaseOrderForm');
            const orderDetails = document.getElementById('orderDetails');
            const searchBtn = document.getElementById('searchBtn');
            const quotationNumber = document.getElementById('quotationNumber');
            const selectedComparisonId = document.getElementById('selected_comparison_id');
            const comparisonDropdown = document.getElementById('comparison_dropdown');
            const comparisonList = document.getElementById('comparison_list');
            const comparisonLoading = document.getElementById('comparison_loading');
            let isSubmitting = false;
            let isNavigatingAway = false;
            let formHasBeenFilled = false;

            @if(session('success'))
                showSweetAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showSweetAlert("{{ session('error') }}", 'error');
            @endif

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

            // Helper function to create dropdown items
            function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                const li = document.createElement('li');
                li.className = className;
                li.textContent = text;
                return li;
            }

            quotationNumber.addEventListener('focus', function () {
                comparisonDropdown.classList.remove('hidden');
                if (comparisonList.children.length === 0) {
                    // Reset pagination
                    comparisonList.dataset.page = "1";
                    comparisonList.dataset.hasMoreData = "true";
                    loadComparisons('');
                }
            });

            document.addEventListener('click', function (e) {
                if (!quotationNumber.contains(e.target) && !comparisonDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                    comparisonDropdown.classList.add('hidden');
                }
            });

            // Add scroll event listener for lazy loading
            comparisonDropdown.addEventListener('scroll', function () {
                // Check if we're already loading or if there's no more data
                if (comparisonList.dataset.loading === "true" || comparisonList.dataset.hasMoreData === "false") return;

                const { scrollTop, scrollHeight, clientHeight } = comparisonDropdown;
                // When user is near the bottom (20px threshold)
                if (scrollTop + clientHeight >= scrollHeight - 20) {
                    loadComparisons(comparisonList.dataset.searchTerm || '');
                }
            });

            const debouncedSearch = debounce(function (e) {
                loadComparisons(e.target.value);
            }, 300);

            quotationNumber.addEventListener('input', debouncedSearch);

            async function loadComparisons(searchTerm) {
                // Setup for lazy loading
                let page = comparisonList.dataset.page ? parseInt(comparisonList.dataset.page) : 1;
                let isLoading = comparisonList.dataset.loading === "true";
                let hasMoreData = comparisonList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || comparisonList.dataset.searchTerm !== searchTerm;
                const loadMoreIndicator = document.getElementById('comparison_load_more');

                // Save current search term
                comparisonList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                comparisonList.dataset.loading = "true";

                // Use different loading indicators based on whether we're resetting or loading more
                if (resetList) {
                    if (comparisonLoading) comparisonLoading.classList.remove('hidden');
                    comparisonList.innerHTML = '';
                } else {
                    if (loadMoreIndicator) loadMoreIndicator.classList.remove('hidden');
                }

                try {
                    // Using the proper endpoint from ProcurementPriceComparisonController
                    const response = await fetch(`{{ route('procurement.price-comparison') }}?json=true&search=${encodeURIComponent(searchTerm || '')}&status=completed&page=${page}&limit=20`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil daftar penawaran');
                    }

                    const result = await response.json();

                    // Handle different response structures
                    let comparisons = [];
                    if (result.success && Array.isArray(result.data)) {
                        comparisons = result.data;
                    } else if (result.comparisons && Array.isArray(result.comparisons)) {
                        comparisons = result.comparisons;
                    } else if (Array.isArray(result)) {
                        comparisons = result;
                    }

                    const purchaseOrderResponse = await fetch(`{{ route("procurement.purchase-order") }}?json=true&limit=1000&search=${encodeURIComponent(searchTerm || '')}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!purchaseOrderResponse.ok) {
                        throw new Error('Gagal mengambil data pemesanan');
                    }

                    const purchaseOrderResult = await purchaseOrderResponse.json();
                    const comparisonsWithPurchaseOrders = new Set();

                    let purchaseOrders = [];
                    if (purchaseOrderResult && purchaseOrderResult.success === true && Array.isArray(purchaseOrderResult.data)) {
                        purchaseOrders = purchaseOrderResult.data;
                    } else if (purchaseOrderResult && Array.isArray(purchaseOrderResult.purchaseOrders)) {
                        purchaseOrders = purchaseOrderResult.purchaseOrders;
                    }

                    if (purchaseOrders && purchaseOrders.length > 0) {
                        purchaseOrders.forEach(po => {
                            if (po && po.comparison_id) {
                                comparisonsWithPurchaseOrders.add(po.comparison_id);
                            }
                        });
                    }

                    // Filter out comparisons that already have purchase orders
                    const filteredComparisons = comparisons.filter(comparison =>
                        !comparisonsWithPurchaseOrders.has(comparison.comparison_id) &&
                        comparison.status &&
                        comparison.status.toLowerCase() === 'completed'
                    );

                    // Check if we have more data to load
                    hasMoreData = filteredComparisons.length === 20;

                    // Save next page number and has more data state
                    comparisonList.dataset.page = page + 1;
                    comparisonList.dataset.hasMoreData = hasMoreData.toString();

                    if (filteredComparisons.length === 0 && comparisonList.children.length === 0) {
                        comparisonList.appendChild(createDropdownItem('Tidak ada penawaran yang tersedia untuk pemesanan', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        filteredComparisons.forEach(comparison => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'comparison-item';

                            const codeSpan = document.createElement('div');
                            codeSpan.className = 'code text-black font-medium';
                            codeSpan.textContent = comparison.comparison_code || '';
                            itemContainer.appendChild(codeSpan);

                            if (comparison.title) {
                                const titleSpan = document.createElement('div');
                                titleSpan.className = 'title text-gray-500 text-sm';
                                titleSpan.textContent = comparison.title;
                                itemContainer.appendChild(titleSpan);
                            }

                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', comparison.comparison_id);
                            li.setAttribute('data-code', comparison.comparison_code);
                            li.setAttribute('data-title', comparison.title || '');
                            li.setAttribute('data-user', comparison.creator?.employee_number || comparison.created_by?.employee_number || '');
                            li.setAttribute('data-date', comparison.created_at || '');
                            li.setAttribute('data-completer', comparison.completer?.employee_number || '');
                            li.setAttribute('data-completed-date', comparison.completed_at || '');

                            li.addEventListener('click', function () {
                                selectedComparisonId.value = this.getAttribute('data-id');
                                quotationNumber.value = this.getAttribute('data-code');
                                comparisonDropdown.classList.add('hidden');
                                document.getElementById('displayComparisonCode').textContent = this.getAttribute('data-code') || '';
                                document.getElementById('displayComparisonTitle').textContent = this.getAttribute('data-title') || '';
                                document.getElementById('displayUserInput').textContent = this.getAttribute('data-user') || 'Staff';
                                document.getElementById('displayComparisonDate').textContent = formatDateIndonesian(this.getAttribute('data-date')) || '';
                                const completerSection = document.getElementById('completerSection');
                                const displayCompleterInput = document.getElementById('displayCompleterInput');
                                const displayCompletedDate = document.getElementById('displayCompletedDate');
                                const completer = this.getAttribute('data-completer');
                                const completedDate = this.getAttribute('data-completed-date');

                                if (completer && completedDate) {
                                    displayCompleterInput.textContent = completer;
                                    displayCompletedDate.textContent = '(' + formatDateIndonesian(completedDate) + ')';
                                    completerSection.style.display = 'table-row';
                                } else {
                                    completerSection.style.display = 'none';
                                }
                            });

                            comparisonList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading price comparisons:', error);
                    if (comparisonList.children.length === 0) {
                        comparisonList.appendChild(createDropdownItem('Gagal memuat daftar penawaran', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    comparisonList.dataset.loading = "false";
                    if (comparisonLoading) comparisonLoading.classList.add('hidden');
                    if (loadMoreIndicator) loadMoreIndicator.classList.add('hidden');
                }
            }

            if (searchBtn) {
                searchBtn.addEventListener('click', function () {
                    if (!quotationNumber.value.trim()) {
                        showSweetAlert('Mohon masukkan nomor penawaran', 'error');
                        return;
                    }

                    if (selectedComparisonId.value && isNaN(parseInt(selectedComparisonId.value, 10))) {
                        showSweetAlert('ID penawaran tidak valid', 'error');
                        return;
                    }

                    const originalBtnText = searchBtn.innerHTML;
                    searchBtn.disabled = true;
                    searchBtn.innerHTML = `
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        `;

                    const comparisonId = selectedComparisonId.value || null;
                    const comparisonCode = quotationNumber.value.trim();

                    if (comparisonId) {
                        fetchComparisonDetails(parseInt(comparisonId, 10))
                            .then(() => {
                                formHasBeenFilled = true;
                            })
                            .finally(() => {
                                searchBtn.disabled = false;
                                searchBtn.innerHTML = originalBtnText;
                            });
                    } else {
                        fetch(`{{ route('procurement.price-comparison') }}?json=true&search=${encodeURIComponent(comparisonCode)}&status=completed`, {
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
                                let comparisons = [];
                                if (result.success && Array.isArray(result.data)) {
                                    comparisons = result.data;
                                } else if (result.comparisons && Array.isArray(result.comparisons)) {
                                    comparisons = result.comparisons;
                                } else if (Array.isArray(result)) {
                                    comparisons = result;
                                }

                                if (comparisons && comparisons.length > 0) {
                                    return fetch(`{{ route("procurement.purchase-order") }}?json=true&limit=1000&search=${encodeURIComponent(comparisonCode)}`, {
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
                                            const comparisonsWithPurchaseOrders = new Set();

                                            let purchaseOrders = [];
                                            if (poResult && poResult.success === true && Array.isArray(poResult.data)) {
                                                purchaseOrders = poResult.data;
                                            } else if (poResult && Array.isArray(poResult.purchaseOrders)) {
                                                purchaseOrders = poResult.purchaseOrders;
                                            }

                                            if (purchaseOrders && purchaseOrders.length > 0) {
                                                purchaseOrders.forEach(po => {
                                                    if (po && po.comparison_id) {
                                                        comparisonsWithPurchaseOrders.add(po.comparison_id);
                                                    }
                                                });
                                            }

                                            const filteredComparisons = comparisons.filter(comparison =>
                                                !comparisonsWithPurchaseOrders.has(comparison.comparison_id) &&
                                                comparison.status &&
                                                comparison.status.toLowerCase() === 'completed'
                                            );

                                            if (filteredComparisons.length === 0) {
                                                throw new Error('Tidak ada penawaran yang tersedia untuk pemesanan atau nomor penawaran sudah memiliki pemesanan');
                                            }

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

                                orderDetails.classList.add('hidden');
                                form.classList.add('hidden');
                            })
                            .finally(() => {
                                searchBtn.disabled = false;
                                searchBtn.innerHTML = originalBtnText;
                            });
                    }
                });
            }

            async function fetchComparisonDetails(comparisonId) {
                try {
                    document.getElementById('assetListTableBody').innerHTML = '';

                    const response = await fetch(`{{ url('procurement/detail-comparison') }}/${comparisonId}`, {
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

                    // Store vendors in window object for later use
                    window.vendors = comparison.vendors || [];

                    document.getElementById('displayComparisonCode').textContent = comparison.comparison_code || '';
                    document.getElementById('displayComparisonTitle').textContent = comparison.title || '';
                    document.getElementById('displayUserInput').textContent = comparison.creator?.employee_name ||
                        comparison.creator?.employee_number ||
                        comparison.created_by?.employee_name ||
                        comparison.created_by?.employee_number ||
                        'Staff';

                    const completerSection = document.getElementById('completerSection');
                    const displayCompleterInput = document.getElementById('displayCompleterInput');
                    const displayCompletedDate = document.getElementById('displayCompletedDate');

                    if (comparison.completer && comparison.completed_at) {
                        displayCompleterInput.textContent = comparison.completer.employee_name || comparison.completer.employee_number || '';
                        displayCompletedDate.textContent = '(' + formatDateIndonesian(comparison.completed_at) + ')';
                        completerSection.style.display = 'table-row';
                    } else {
                        completerSection.style.display = 'none';
                    }

                    let displayDate = comparison.created_at || '';
                    if (displayDate) {
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

                    const uniqueVendors = {};
                    if (comparison.items && Array.isArray(comparison.items)) {
                        comparison.items.forEach(item => {
                            if (item.vendor_offers && Array.isArray(item.vendor_offers)) {
                                item.vendor_offers.forEach(offer => {
                                    if (offer.vendor && offer.vendor.vendor_id) {
                                        const vendorId = offer.vendor.vendor_id;
                                        if (!uniqueVendors[vendorId]) {
                                            uniqueVendors[vendorId] = offer.vendor;
                                            uniqueVendors[vendorId].payment_terms = offer.agreement?.payment_terms || offer.payment_terms || '';
                                            uniqueVendors[vendorId].delivery_terms = offer.agreement?.delivery_terms || offer.delivery_terms || '';
                                        }
                                    }
                                });
                            }
                        });
                    }

                    populateAssetList(comparison.items || [], Object.values(uniqueVendors));

                    orderDetails.classList.remove('hidden');
                    form.classList.remove('hidden');

                } catch (error) {
                    console.error('Error fetching comparison details:', error);
                    showSweetAlert('Gagal memuat detail penawaran: ' + error.message, 'error');

                    orderDetails.classList.add('hidden');
                    form.classList.add('hidden');
                }
            }

            function populateAssetList(items, vendors) {
                const tableContainer = document.querySelector('.overflow-x-auto');
                tableContainer.innerHTML = '';
                const table = document.createElement('table');
                table.className = 'w-full';
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');

                const headers = [
                    { text: 'Nama Aset', align: 'left' },
                    { text: 'Jumlah', align: 'center' },
                    { text: 'Perkiraan Harga', align: 'left' }
                ];

                if (vendors && vendors.length > 0) {
                    vendors.forEach(vendor => {
                        headers.push({ text: vendor.vendor_name || 'Vendor', align: 'left' });
                    });
                }

                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.className = `bg-[#213268] text-white p-3 font-bold text-sm text-${header.align}`;
                    th.textContent = header.text;
                    headerRow.appendChild(th);
                });

                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                tbody.id = 'assetListTableBody';

                if (!items || items.length === 0) {
                    const row = document.createElement('tr');
                    row.className = 'border-t border-[#EEF1F4]';
                    const cell = document.createElement('td');
                    cell.className = 'p-3 text-sm text-[#666666] text-center';
                    cell.colSpan = 3 + (vendors.length || 0);
                    cell.textContent = 'Tidak ada item ditemukan untuk penawaran ini';
                    row.appendChild(cell);
                    tbody.appendChild(row);
                    addPaymentAndDeliveryRows(tbody, [])
                } else {
                    items.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = 'border-t border-[#EEF1F4]';

                        // Name cell
                        const nameCell = document.createElement('td');
                        nameCell.className = 'p-3 text-sm text-[#666666]';
                        nameCell.textContent = item.procurement_item_name || 'Item ' + (index + 1);
                        row.appendChild(nameCell);

                        // Quantity cell
                        const qtyCell = document.createElement('td');
                        qtyCell.className = 'p-3 text-sm text-center text-[#666666]';
                        qtyCell.textContent = item.quantity || 1;
                        row.appendChild(qtyCell);

                        // Estimated price cell with flex layout
                        const estPriceCell = document.createElement('td');
                        estPriceCell.className = 'p-3 text-sm text-[#666666]';

                        const estPriceContainer = document.createElement('div');
                        estPriceContainer.className = 'flex flex-col space-y-1';

                        const estUnitPrice = parseFloat(item.estimated_unit_price || 0);
                        const estPrice = estUnitPrice * parseInt(item.quantity);

                        // Total price div with blue background
                        const totalPriceDiv = document.createElement('div');
                        totalPriceDiv.className = 'flex items-center justify-between bg-blue-50 px-2 py-0.5 rounded';

                        const totalLabel = document.createElement('span');
                        totalLabel.className = 'text-xs font-medium text-[#213268]';
                        totalLabel.textContent = 'Total:';

                        const totalValue = document.createElement('span');
                        totalValue.className = 'text-sm font-bold';
                        totalValue.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(estPrice);

                        totalPriceDiv.appendChild(totalLabel);
                        totalPriceDiv.appendChild(totalValue);
                        estPriceContainer.appendChild(totalPriceDiv);

                        // Unit price div with gray background
                        const unitPriceDiv = document.createElement('div');
                        unitPriceDiv.className = 'flex items-center justify-between bg-gray-50 px-2 py-0.5 rounded';

                        const unitLabel = document.createElement('span');
                        unitLabel.className = 'text-xs font-medium text-gray-600';
                        unitLabel.textContent = 'Harga Satuan:';

                        const unitValue = document.createElement('span');
                        unitValue.className = 'text-sm';
                        unitValue.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(estUnitPrice);

                        unitPriceDiv.appendChild(unitLabel);
                        unitPriceDiv.appendChild(unitValue);
                        estPriceContainer.appendChild(unitPriceDiv);

                        estPriceCell.appendChild(estPriceContainer);
                        row.appendChild(estPriceCell);

                        // Vendor cells
                        if (vendors && vendors.length > 0) {
                            vendors.forEach(vendor => {
                                const vendorCell = document.createElement('td');
                                vendorCell.className = 'p-3 text-sm text-[#666666]';

                                let vendorOffer = null;
                                if (item.vendor_offers && Array.isArray(item.vendor_offers)) {
                                    vendorOffer = item.vendor_offers.find(offer =>
                                        offer.vendor && offer.vendor.vendor_id == vendor.vendor_id
                                    );
                                }

                                if (vendorOffer) {
                                    // Create a container for the radio button and price info
                                    const vendorContainer = document.createElement('div');
                                    vendorContainer.className = 'flex items-start gap-3';

                                    const radioWrapper = document.createElement('div');
                                    radioWrapper.className = 'mt-2';

                                    const radioInput = document.createElement('input');
                                    radioInput.type = 'radio';
                                    radioInput.name = `vendor_item${item.price_comparison_item_id}`;
                                    radioInput.value = vendor.vendor_id;
                                    radioInput.className = 'h-5 w-5 text-[#213268] border-gray-300 focus:ring-[#213268]';
                                    radioInput.dataset.item_id = item.price_comparison_item_id;
                                    radioInput.dataset.vendor_id = vendor.vendor_id;
                                    radioInput.dataset.vendor_offer_id = vendorOffer.vendor_offer_id;

                                    // Create price container
                                    const priceContainer = document.createElement('div');
                                    priceContainer.className = 'flex flex-col space-y-1.5 flex-grow';

                                    const unitPrice = parseFloat(vendorOffer.unit_price || 0);
                                    const price = unitPrice * parseInt(item.quantity || 1);

                                    // Total price div with blue background
                                    const totalDiv = document.createElement('div');
                                    totalDiv.className = 'flex items-center justify-between bg-blue-50 px-3 py-1 rounded';

                                    const totalLabel = document.createElement('span');
                                    totalLabel.className = 'text-xs font-medium text-[#213268]';
                                    totalLabel.textContent = 'Total:';

                                    const totalValue = document.createElement('span');
                                    totalValue.className = 'text-sm font-semibold';
                                    totalValue.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

                                    totalDiv.appendChild(totalLabel);
                                    totalDiv.appendChild(totalValue);
                                    priceContainer.appendChild(totalDiv);

                                    // Unit price div with gray background
                                    const unitDiv = document.createElement('div');
                                    unitDiv.className = 'flex items-center justify-between bg-gray-50 px-3 py-1 rounded';

                                    const unitLabel = document.createElement('span');
                                    unitLabel.className = 'text-xs font-medium text-gray-600';
                                    unitLabel.textContent = 'Harga Satuan:';

                                    const unitValue = document.createElement('span');
                                    unitValue.className = 'text-sm text-gray-700';
                                    unitValue.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(unitPrice);

                                    unitDiv.appendChild(unitLabel);
                                    unitDiv.appendChild(unitValue);
                                    priceContainer.appendChild(unitDiv);

                                    // Add additional info if available
                                    if (vendorOffer.additional_info) {
                                        const infoDiv = document.createElement('div');
                                        infoDiv.className = 'bg-yellow-50 px-3 py-1 rounded';

                                        const infoLabel = document.createElement('div');
                                        infoLabel.className = 'text-xs font-medium text-gray-600';
                                        infoLabel.textContent = 'Info:';

                                        const infoValue = document.createElement('div');
                                        infoValue.className = 'text-xs text-gray-700';
                                        infoValue.textContent = vendorOffer.additional_info;

                                        infoDiv.appendChild(infoLabel);
                                        infoDiv.appendChild(infoValue);
                                        priceContainer.appendChild(infoDiv);
                                    }

                                    // Assemble the cell content
                                    radioWrapper.appendChild(radioInput);
                                    vendorContainer.appendChild(radioWrapper);
                                    vendorContainer.appendChild(priceContainer);
                                    vendorCell.appendChild(vendorContainer);
                                } else {
                                    const notAvailableDiv = document.createElement('div');
                                    notAvailableDiv.className = 'text-sm font-medium text-gray-400 p-2';
                                    notAvailableDiv.textContent = 'Tidak Tersedia';
                                    vendorCell.appendChild(notAvailableDiv);
                                }

                                row.appendChild(vendorCell);
                            });
                        }

                        tbody.appendChild(row);
                    });
                    addPaymentAndDeliveryRows(tbody, vendors);
                }

                table.appendChild(tbody);
                tableContainer.appendChild(table);
                const radioInputs = document.querySelectorAll('input[type="radio"]');
                radioInputs.forEach(input => {
                    input.addEventListener('change', () => {
                        formHasBeenFilled = true;
                    });
                });
                const notesField = document.getElementById('notes');
                if (notesField) {
                    notesField.addEventListener('input', () => {
                        formHasBeenFilled = true;
                    });
                }
            }

            function addPaymentAndDeliveryRows(tbody, vendors) {
                const paymentRow = document.createElement('tr');
                paymentRow.className = 'border-t border-[#EEF1F4] bg-[#E9ECF6]';
                const paymentLabelCell = document.createElement('td');
                paymentLabelCell.className = 'p-3 text-sm font-medium text-[#213268]';
                paymentLabelCell.textContent = 'Syarat Pembayaran';
                paymentRow.appendChild(paymentLabelCell);
                const paymentEmptyCell = document.createElement('td');
                paymentEmptyCell.colSpan = 2;
                paymentEmptyCell.className = 'p-3';
                paymentRow.appendChild(paymentEmptyCell);
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
                const deliveryRow = document.createElement('tr');
                deliveryRow.className = 'border-t border-[#EEF1F4] bg-[#E9ECF6]';
                const deliveryLabelCell = document.createElement('td');
                deliveryLabelCell.className = 'p-3 text-sm font-medium text-[#213268]';
                deliveryLabelCell.textContent = 'Syarat Pengiriman';
                deliveryRow.appendChild(deliveryLabelCell);
                const deliveryEmptyCell = document.createElement('td');
                deliveryEmptyCell.colSpan = 2;
                deliveryEmptyCell.className = 'p-3';
                deliveryRow.appendChild(deliveryEmptyCell);
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

            if (form) {
                document.getElementById('submitOrderBtn').addEventListener('click', function (e) {
                    if (isSubmitting) {
                        return;
                    }
                    const selectedItems = [];
                    const selectedVendors = new Set();
                    const radioInputs = document.querySelectorAll('input[type="radio"]:checked');

                    if (radioInputs.length === 0) {
                        showSweetAlert('Mohon pilih vendor untuk setidaknya satu item', 'error');
                        return;
                    }
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

                    const notes = document.getElementById('notes').value;
                    let paymentTerms = '';
                    let deliveryTerms = '';
                    if (selectedVendors.size > 0) {
                        const firstVendorId = Array.from(selectedVendors)[0];
                        const selectedVendor = window.vendors.find(v => v.vendor_id == firstVendorId);

                        if (selectedVendor) {
                            paymentTerms = selectedVendor.payment_terms || '';
                            deliveryTerms = selectedVendor.delivery_terms || '';
                        }
                    }

                    const purchaseOrderData = {
                        comparison_id: parseInt(selectedComparisonId.value),
                        selections: selectedItems,
                        notes: notes
                    };

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
                            MENYIMPAN...
                        `;

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
                                        isNavigatingAway = true;
                                    },
                                    willClose: () => {
                                        window.location.href = "{{ route('procurement.purchase-order') }}";
                                    }
                                });
                            } else {
                                isSubmitting = false;
                                submitButton.disabled = false;
                                submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitButton.innerHTML = originalButtonText;

                                let errorMessage = data.errors || 'Gagal membuat Pesanan Pembelian';
                                if (typeof errorMessage === 'object') {
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

            function formHasChanges() {
                if (formHasBeenFilled) return true;

                if (document.getElementById('selected_comparison_id').value ||
                    document.getElementById('quotationNumber').value.trim()) {
                    return true;
                }

                const notes = document.getElementById('notes');
                if (notes && notes.value.trim()) {
                    return true;
                }

                const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
                if (selectedRadios.length > 0) {
                    return true;
                }

                return false;
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
                            window.location.href = '{{ route("procurement.purchase-order") }}';
                        }
                    });
                } else {
                    window.location.href = '{{ route("procurement.purchase-order") }}';
                }
            });

            window.addEventListener('beforeunload', function (e) {
                if (!isNavigatingAway && formHasChanges()) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

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

                        /* Comparison dropdown styles */
                        .comparison-item {
                            display: flex;
                            flex-direction: column;
                        }
                        .comparison-item .code {
                            font-weight: 500;
                        }
                        .comparison-item .title {
                            font-size: 0.8rem;
                            color: #666;
                        }

                        /* Animation for new items */
                        @keyframes fadeIn {
                            from { opacity: 0; transform: translateY(5px); }
                            to { opacity: 1; transform: translateY(0); }
                        }
                        #comparison_list li {
                            animation: fadeIn 0.2s ease-out forwards;
                        }

                            /* Custom radio button styling */
                            input[type="radio"] {
                                -webkit-appearance: none;
                                -moz-appearance: none;
                                appearance: none;
                                width: 1rem;
                                height: 1rem;
                                border: 2px solid #ccc;
                                border-radius: 50%;
                                outline: none;
                                cursor: pointer;
                            }

                            input[type="radio"]:checked {
                                border-color: #213268;
                                background-color: white;
                                box-shadow: inset 0 0 0 3px #213268;
                            }

                                </style>
                            `);
        });
    </script>
@endpush
