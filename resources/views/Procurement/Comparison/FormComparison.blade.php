@extends('Layout.app')

@section('title', 'Formulir Perbandingan Harga')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Price Comparison Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.price-comparison') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">FORMULIR PERBANDINGAN HARGA</h1>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="space-y-4">
                    <!-- Quotation Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Judul Penawaran</label>
                        <input type="text" id="comparisonTitle" value=""
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Pembelian toner printer">
                    </div>

                    <!-- Request Number -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Nomor Permintaan</label>
                        <div class="relative">
                            <input type="text" id="requestNumber" value=""
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Masukkan nomor permintaan yang disetujui" autocomplete="off">
                            <input type="hidden" id="selected_request_id">

                            <div class="absolute inset-y-0 right-0 flex">
                            <button id="searchBtn" type="button" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            </div>

                            <!-- Dropdown for search results -->
                            <div id="procurement_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                <!-- Loading indicator -->
                                <div id="procurement_loading" class="flex justify-center py-2">
                                    <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <ul id="procurement_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Details Section - Hidden by default -->
                <div id="requestDetails" class="hidden">
                    <!-- Request Details -->
                    <div class="grid grid-cols-1 gap-5">
                        <!-- Request Number -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Nomor Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayRequestNumber">PPB2406002</span></p>
                        </div>

                        <!-- Request Title -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Judul Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayRequestName">Pembelian Komputer IT</span></p>
                        </div>

                        <!-- Requester -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Pemohon</p>
                            <p class="text-[#666666]">: <span id="displayUserInput">Karyawan</span></p>
                        </div>

                        <!-- Request Date -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Tanggal Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayInputDate">2024-06-30 06:56:02</span></p>
                        </div>
                    </div>

                    <!-- Asset List -->
                    <div class="space-y-4 mt-6">
                        <h2 class="text-lg font-semibold text-[#666666]">Daftar Aset</h2>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">NAMA ASET</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">SPESIFIKASI</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">JML</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">HARGA SATUAN</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="assetListTableBody">
                                    <!-- Asset items will be populated here through JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-4 mt-8">
                        <button type="button" id="submitBtn" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            KIRIM
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50"></div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const requestNumber = document.getElementById('requestNumber');
        const comparisonTitle = document.getElementById('comparisonTitle');
        const submitBtn = document.getElementById('submitBtn');
        const procurementDropdown = document.getElementById('procurement_dropdown');
        const procurementList = document.getElementById('procurement_list');
        const procurementLoading = document.getElementById('procurement_loading');
        const selectedRequestId = document.getElementById('selected_request_id');
        const requestDetails = document.getElementById('requestDetails');

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
            notification.className = 'p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.role = 'alert';

            // Check if message contains HTML
            const hasHTML = /<[a-z][\s\S]*>/i.test(message);

            if (type === 'success') {
                notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                notification.innerHTML = `
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
                notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                // Structure for the notification
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

                // Handle HTML content or convert to nested list if it's an object
                if (typeof message === 'object') {
                    // Create an unordered list for nested errors
                    const errorList = document.createElement('ul');
                    errorList.className = 'list-disc pl-5 mt-2 space-y-1';

                    // Process each error
                    Object.entries(message).forEach(([key, value]) => {
                        const listItem = document.createElement('li');

                        if (Array.isArray(value)) {
                            // If the value is an array, create a nested list
                            const keyText = document.createElement('span');
                            keyText.className = 'font-medium';
                            keyText.textContent = key + ': ';
                            listItem.appendChild(keyText);

                            const nestedList = document.createElement('ul');
                            nestedList.className = 'list-disc pl-5 mt-1';

                            value.forEach(item => {
                                const nestedItem = document.createElement('li');
                                nestedItem.textContent = item;
                                nestedList.appendChild(nestedItem);
                            });

                            listItem.appendChild(nestedList);
                        } else {
                            // Simple key-value pair
                            listItem.textContent = `${key}: ${value}`;
                        }

                        errorList.appendChild(listItem);
                    });

                    messageContainer.appendChild(errorList);
                } else if (hasHTML) {
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
                    notification.remove();
                };

                // Assemble the notification
                wrapper.appendChild(iconContainer);
                wrapper.appendChild(contentContainer);
                wrapper.appendChild(closeBtn);
                notification.appendChild(wrapper);
            }

            // Add to document
            document.getElementById('toast-container').appendChild(notification);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }

        // Search button click event
        searchBtn.addEventListener('click', function() {
            // Validate inputs
            if (!requestNumber.value.trim()) {
                showToast('Mohon masukkan Nomor Pengajuan yang sudah disetujui', 'error');
                return;
            }

            // If a selected ID is available, ensure it's a valid number
            if (selectedRequestId.value && isNaN(parseInt(selectedRequestId.value, 10))) {
                showToast('ID Pengajuan tidak valid', 'error');
                return;
            }

            // Show loading indicator on button
            const originalBtnText = searchBtn.innerHTML;
            searchBtn.disabled = true;
            searchBtn.innerHTML = `
                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            `;

            // Get the procurement ID directly from the request number
            const procurementCode = requestNumber.value.trim();

            // If the user has selected an ID from the dropdown, use that
            if (selectedRequestId.value) {
                fetchProcurementDetails(parseInt(selectedRequestId.value, 10))
                    .finally(() => {
                        // Reset button state
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = originalBtnText;
                    });
            } else {
                // Otherwise, try to fetch by code
                fetch(`{{ route('procurement.search') }}?search=${encodeURIComponent(procurementCode)}&status=approved`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mencari data pengajuan');
                        return response.json();
                    })
                    .then(result => {
                        if (result.success && result.data && result.data.length > 0) {
                            // Find exact match by code if possible
                            const exactMatch = result.data.find(item =>
                                item.procurement_code &&
                                item.procurement_code.toLowerCase() === procurementCode.toLowerCase() &&
                                item.status &&
                                item.status.toLowerCase() === 'approved');

                            if (exactMatch) {
                                selectedRequestId.value = exactMatch.procurement_id;
                                return fetchProcurementDetails(parseInt(exactMatch.procurement_id, 10));
                            } else {
                                // Find the first approved result
                                const approvedMatch = result.data.find(item =>
                                    item.status &&
                                    item.status.toLowerCase() === 'approved');

                                if (approvedMatch) {
                                    selectedRequestId.value = approvedMatch.procurement_id;
                                    return fetchProcurementDetails(parseInt(approvedMatch.procurement_id, 10));
                                } else {
                                    throw new Error('Nomor pengajuan yang disetujui tidak ditemukan, silakan periksa kembali nomor pengajuan');
                                }
                            }
                        } else {
                            throw new Error('Nomor pengajuan yang disetujui tidak ditemukan, silakan periksa kembali nomor pengajuan');
                        }
                    })
                    .catch(error => {
                        console.error('Error searching for procurement:', error);
                        showToast(error.message || 'Terjadi kesalahan saat mencari data pengajuan', 'error');

                        // Hide details section if there was an error
                        requestDetails.classList.add('hidden');
                    })
                    .finally(() => {
                        // Reset button state
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = originalBtnText;
                    });
            }
        });

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
        requestNumber.addEventListener('focus', function() {
            procurementDropdown.classList.remove('hidden');
            if (procurementList.children.length === 0) {
                loadProcurements(''); // Initial load on focus
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!requestNumber.contains(e.target) && !procurementDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                procurementDropdown.classList.add('hidden');
            }
        });

        // Search input handler with debounce
        const debouncedSearch = debounce(function(e) {
            loadProcurements(e.target.value);
        }, 300);

        requestNumber.addEventListener('input', debouncedSearch);

        // Function to load procurement requests
        async function loadProcurements(searchTerm) {
            // Show loading indicator
            if (procurementLoading) procurementLoading.classList.remove('hidden');
            procurementList.innerHTML = '';

            try {
                // Fetch procurement data from API with approved status filter
                const response = await fetch(`{{ route('procurement.search') }}?search=${encodeURIComponent(searchTerm)}&status=approved`);

                if (!response.ok) {
                    throw new Error('Gagal mengambil daftar permintaan');
                }

                const result = await response.json();
                let procurements = result.data || [];

                // Populate dropdown
                procurementList.innerHTML = '';

                if (procurements.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-4 py-2 text-gray-500 italic';
                    noResults.textContent = 'Tidak ada permintaan yang disetujui ditemukan';
                    procurementList.appendChild(noResults);
                } else {
                    procurements.forEach(procurement => {
                        // Skip non-approved procurements (extra safety check)
                        if (procurement.status && procurement.status.toLowerCase() !== 'approved') {
                            return;
                        }

                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        // Format display text - only show procurement code
                        const displayText = procurement.procurement_code || '';

                        li.textContent = displayText;
                        li.setAttribute('data-id', procurement.procurement_id);
                        li.setAttribute('data-code', procurement.procurement_code);
                        li.setAttribute('data-name', procurement.procurement_name || '');
                        li.setAttribute('data-user', procurement.user_name || 'Karyawan');
                        li.setAttribute('data-date', procurement.created_at || '');

                        li.addEventListener('click', function() {
                            // Set the selected procurement values
                            selectedRequestId.value = this.getAttribute('data-id');
                            requestNumber.value = this.getAttribute('data-code');

                            // Hide dropdown
                            procurementDropdown.classList.add('hidden');
                        });

                        procurementList.appendChild(li);
                    });
                }
            } catch (error) {
                console.error('Error loading procurement requests:', error);
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = 'Gagal memuat daftar permintaan';
                procurementList.appendChild(errorItem);
            } finally {
                if (procurementLoading) procurementLoading.classList.add('hidden');
            }
        }

        // Function to fetch procurement details by ID
        async function fetchProcurementDetails(procurementId) {
            try {
                // Show loading state
                if (submitBtn) submitBtn.disabled = true;

                // Clear existing data in case of re-fetch
                document.getElementById('assetListTableBody').innerHTML = '';

                const response = await fetch(`{{ url('procurement/request') }}/${procurementId}`);

                if (!response.ok) {
                    throw new Error('Gagal mengambil detail permintaan');
                }

                const result = await response.json();

                if (!result.success || !result.data) {
                    throw new Error(result.errors?.general || 'Data tidak valid dari server');
                }

                const procurement = result.data;

                console.log('Procurement data:', procurement);

                // Update the form with procurement details
                document.getElementById('displayRequestNumber').textContent = procurement.procurement_code || '';
                document.getElementById('displayRequestName').textContent = procurement.title || procurement.procurement_name || '';
                document.getElementById('displayUserInput').textContent = procurement.requester?.employee_number || procurement.user_name || 'Karyawan';

                // Format date properly
                let displayDate = procurement.request_date || procurement.created_at || '';
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
                document.getElementById('displayInputDate').textContent = displayDate;

                // If the title field is empty, use the procurement name
                if (!comparisonTitle.value) {
                    comparisonTitle.value = procurement.title || procurement.procurement_name || '';
                }

                // Get items from the right property (either details or items)
                const items = procurement.details || procurement.items || [];

                // Populate asset list table
                populateAssetList(items);

            // Show the request details section
            requestDetails.classList.remove('hidden');

                // Re-enable the submit button if it exists
                if (submitBtn) submitBtn.disabled = false;
            } catch (error) {
                console.error('Error fetching procurement details:', error);
                showToast('Gagal memuat detail permintaan: ' + error.message, 'error');
            }
        }

        // Function to populate asset list table
        function populateAssetList(items) {
            const tableBody = document.getElementById('assetListTableBody');
            tableBody.innerHTML = '';

            let grandTotal = 0;

            if (!items || items.length === 0) {
                // If no items, show a message
                const row = document.createElement('tr');
                row.className = 'border-t border-[#EEF1F4]';

                const cell = document.createElement('td');
                cell.className = 'p-3 text-xs text-[#666666] text-center';
                cell.colSpan = 5;
                cell.textContent = 'Tidak ada item ditemukan untuk permintaan ini';

                row.appendChild(cell);
                tableBody.appendChild(row);
                return;
            }

            // Add each item as a row
            items.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-t border-[#EEF1F4]';

                // Format item data
                const assetName = item.asset_name || 'Aset Tidak Diketahui';
                const specification = item.specifications || item.specification || '';
                const quantity = item.quantity || 0;

                // Format currency
                const unitPrice = parseFloat(item.estimated_unit_price || item.unit_price || 0);
                const total = quantity * unitPrice;
                grandTotal += total;

                const formatter = new Intl.NumberFormat('id-ID');

                // Create and append cells
                const nameCell = document.createElement('td');
                nameCell.className = 'p-3 text-xs text-[#666666]';
                nameCell.textContent = assetName;
                row.appendChild(nameCell);

                const specCell = document.createElement('td');
                specCell.className = 'p-3 text-xs text-[#666666]';
                specCell.textContent = specification;
                row.appendChild(specCell);

                const qtyCell = document.createElement('td');
                qtyCell.className = 'p-3 text-xs text-center text-[#666666]';
                qtyCell.textContent = quantity;
                row.appendChild(qtyCell);

                const priceCell = document.createElement('td');
                priceCell.className = 'p-3 text-xs text-left text-[#666666]';
                priceCell.textContent = formatter.format(unitPrice);
                row.appendChild(priceCell);

                const totalCell = document.createElement('td');
                totalCell.className = 'p-3 text-xs text-left text-[#666666]';
                totalCell.textContent = formatter.format(total);
                row.appendChild(totalCell);

                tableBody.appendChild(row);
            });

            // Add grand total row
            const totalRow = document.createElement('tr');
            totalRow.className = 'border-t border-[#EEF1F4]';

            const totalLabelCell = document.createElement('td');
            totalLabelCell.className = 'p-3 text-xs font-medium text-right text-[#666666]';
            totalLabelCell.colSpan = 4;
            totalLabelCell.textContent = 'Total Keseluruhan';
            totalRow.appendChild(totalLabelCell);

            const totalValueCell = document.createElement('td');
            totalValueCell.className = 'p-3 text-xs font-medium text-left text-[#666666]';
            totalValueCell.textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
            totalRow.appendChild(totalValueCell);

            tableBody.appendChild(totalRow);
        }

        // Submit button click event
        if (submitBtn) {
            let isSubmitting = false; // Flag to track submission status
            submitBtn.addEventListener('click', function() {
                // Prevent multiple submissions
                if (isSubmitting) {
                    return;
                }

                // Validate inputs
                if (!comparisonTitle.value.trim()) {
                    showToast('Mohon masukkan judul penawaran', 'error');
                    return;
                }

                if (!selectedRequestId.value) {
                    showToast('Mohon pilih permintaan pengadaan', 'error');
                    return;
                }

                // Create form data
                const formData = new FormData();
                formData.append('title', comparisonTitle.value);
                formData.append('procurement_id', parseInt(selectedRequestId.value, 10));

                // Set submitting flag and disable button
                isSubmitting = true;

                // Disable submit button during submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    Mengirim...
                `;

                // Submit the form via AJAX
                fetch('{{ route('procurement.store-price-comparison') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success toast - don't reset button or submitting flag since we're redirecting
                        showToast(data.message || 'Perbandingan harga berhasil dibuat!');

                        // Set a flag to indicate we're intentionally navigating away
                        const isNavigatingAway = true;

                        // Redirect after success
                        setTimeout(() => {
                            window.location.href = data.redirect_url ||
                                `{{ route('procurement.price-comparison') }}`;
                        }, 1500);
                    } else {
                        // Reset submission flag and button on error
                        isSubmitting = false;
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'KIRIM';

                        if (data.errors) {
                            showToast(data.errors, 'error');
                        } else {
                            showToast(data.message || 'Terjadi kesalahan saat membuat perbandingan harga', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error creating price comparison:', error);

                    // Reset flag and button on error
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'KIRIM';

                    if (error.errors) {
                        showToast(error.errors, 'error');
                    } else {
                        showToast(error.message || 'Terjadi kesalahan saat membuat perbandingan harga', 'error');
                    }
                });
            });
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
