@extends('Layout.app')

@section('title', isset($comparison) ? 'Edit Perbandingan Harga' : 'Formulir Perbandingan Harga')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">{{ isset($comparison) ? 'EDIT PERBANDINGAN HARGA' : 'FORMULIR PERBANDINGAN HARGA' }}</h1>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="space-y-4">
                    <!-- Quotation Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Judul Penawaran <span class="text-red-500">*</span></label>
                        <input type="text" id="comparisonTitle" value="{{ $comparison['title'] ?? '' }}"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Pembelian toner printer">
                        <div class="error-message text-red-500 text-sm mt-1 hidden">Judul penawaran harus diisi</div>
                    </div>

                    <!-- Request Number -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Nomor Permintaan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="requestNumber" value="{{ isset($comparison['procurement']) ? $comparison['procurement']['procurement_code'] ?? '' : '' }}"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Masukkan nomor permintaan yang disetujui" autocomplete="off" {{ isset($comparison) ? 'readonly' : '' }}>
                            <input type="hidden" id="selected_request_id" value="{{ $comparison['procurement_id'] ?? '' }}">

                            <div class="absolute inset-y-0 right-0 flex">
                            <button id="searchBtn" type="button" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor permintaan harus diisi</div>

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

                <!-- Request Details Section - Hidden by default unless editing -->
                <div id="requestDetails" class="{{ isset($comparison) ? '' : 'hidden' }}">
                    <!-- Request Details -->
                    <div class="grid grid-cols-1 gap-5">
                        <!-- Request Number -->
                        <div class="flex items-start gap-2">
                            <p class="w-40 text-[#666666] font-medium">Nomor Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayRequestNumber">{{ isset($comparison['procurement']) ? $comparison['procurement']['procurement_code'] ?? '' : '' }}</span></p>
                        </div>

                        <!-- Request Title -->
                        <div class="flex items-start gap-2">
                            <p class="w-40 text-[#666666] font-medium">Judul Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayRequestName">{{ isset($comparison['procurement']) ? $comparison['procurement']['procurement_name'] ?? '' : '' }}</span></p>
                        </div>

                        <!-- Requester -->
                        <div class="flex items-start gap-2">
                            <p class="w-40 text-[#666666] font-medium">Pemohon</p>
                            <p class="text-[#666666]">: <span id="displayUserInput">{{ isset($comparison['procurement']) ? $comparison['procurement']['user_name'] ?? '' : '' }}</span></p>
                        </div>

                        <!-- Request Date -->
                        <div class="flex items-start gap-2">
                            <p class="w-40 text-[#666666] font-medium">Tanggal Permintaan</p>
                            <p class="text-[#666666]">: <span id="displayInputDate">
                                @if(isset($comparison['procurement']['created_at']))
                                    {{ \Carbon\Carbon::parse($comparison['procurement']['created_at'])->format('d F Y') }}
                                @endif
                            </span></p>
                        </div>
                    </div>

                    <!-- Asset List -->
                    <div class="space-y-4 mt-6">
                        <h2 class="text-lg font-semibold text-[#666666]">Daftar Aset</h2>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">NAMA ASET</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">SPESIFIKASI</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">JML</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">HARGA SATUAN</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">TOTAL</th>
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
                            {{ isset($comparison) ? 'SIMPAN' : 'KIRIM' }}
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

        // Check if we're in edit mode
        const isEditMode = {{ isset($comparison) ? 'true' : 'false' }};
        const comparisonId = {{ $comparison['comparison_id'] ?? 'null' }};

        // If in edit mode and there's a procurement ID, load its details
        if (isEditMode && selectedRequestId.value) {
            fetchProcurementDetails(parseInt(selectedRequestId.value, 10));
        }

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
        function showSweetAlert(message, type = 'success') {
            const iconMap = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info',
                question: 'question'
            };

            // Default options
            const options = {
                title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi',
                html: message,
                icon: iconMap[type] || 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#213268',
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

            // Add specific options based on alert type
            if (type === 'success') {
                // Auto close success messages after 2.5 seconds
                options.timer = 2500;
                options.timerProgressBar = true;
            } else if (type === 'error') {
                // Make error alerts more prominent
                options.confirmButtonColor = '#d33';
                options.showCloseButton = true;
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

            // Fire the alert
            Swal.fire(options);
        }

        // Function to validate field
        function validateField(field) {
            const errorElement = field.closest('.space-y-2').querySelector('.error-message');

            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                if (errorElement) errorElement.classList.remove('hidden');
                return false;
            } else {
                field.classList.remove('border-red-500');
                if (errorElement) errorElement.classList.add('hidden');
                return true;
            }
        }

        // Add input event listeners to clear error styling on fields
        comparisonTitle.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        requestNumber.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        // Search button click event
        searchBtn.addEventListener('click', function() {
            // Validate inputs
            if (!requestNumber.value.trim()) {
                requestNumber.classList.add('border-red-500');
                const errorElement = requestNumber.closest('.space-y-2').querySelector('.error-message');
                if (errorElement) errorElement.classList.remove('hidden');
                showSweetAlert('Mohon masukkan Nomor Pengajuan yang sudah disetujui', 'error');
                return;
            }

            // If a selected ID is available, ensure it's a valid number
            if (selectedRequestId.value && isNaN(parseInt(selectedRequestId.value, 10))) {
                showSweetAlert('ID Pengajuan tidak valid', 'error');
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
                        showSweetAlert(error.message || 'Terjadi kesalahan saat mencari data pengajuan', 'error');

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
            // Don't show dropdown in edit mode
            if (!isEditMode) {
            procurementDropdown.classList.remove('hidden');
            if (procurementList.children.length === 0) {
                loadProcurements(''); // Initial load on focus
                }
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
            if (!isEditMode) { // Only load in create mode
            loadProcurements(e.target.value);
            }
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
                            displayDate = formatDateIndonesian(displayDate);
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
                showSweetAlert('Gagal memuat detail permintaan: ' + error.message, 'error');
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
                cell.className = 'p-3 text-sm text-[#666666] text-center';
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
                nameCell.className = 'p-3 text-sm text-[#666666]';
                nameCell.textContent = assetName;
                row.appendChild(nameCell);

                const specCell = document.createElement('td');
                specCell.className = 'p-3 text-sm text-[#666666]';
                specCell.textContent = specification;
                row.appendChild(specCell);

                const qtyCell = document.createElement('td');
                qtyCell.className = 'p-3 text-sm text-center text-[#666666]';
                qtyCell.textContent = quantity;
                row.appendChild(qtyCell);

                const priceCell = document.createElement('td');
                priceCell.className = 'p-3 text-sm text-left text-[#666666]';
                priceCell.textContent = formatter.format(unitPrice);
                row.appendChild(priceCell);

                const totalCell = document.createElement('td');
                totalCell.className = 'p-3 text-sm text-left text-[#666666]';
                totalCell.textContent = formatter.format(total);
                row.appendChild(totalCell);

                tableBody.appendChild(row);
            });

            // Add grand total row
            const totalRow = document.createElement('tr');
            totalRow.className = 'border-t border-[#EEF1F4]';

            const totalLabelCell = document.createElement('td');
            totalLabelCell.className = 'p-3 text-sm font-medium text-right text-[#666666]';
            totalLabelCell.colSpan = 4;
            totalLabelCell.textContent = 'Total Keseluruhan';
            totalRow.appendChild(totalLabelCell);

            const totalValueCell = document.createElement('td');
            totalValueCell.className = 'p-3 text-sm font-medium text-left text-[#666666]';
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
                    showSweetAlert('Mohon masukkan judul penawaran', 'error');
                    return;
                }

                if (!selectedRequestId.value) {
                    showSweetAlert('Mohon pilih permintaan pengadaan', 'error');
                    return;
                }

                // Create form data
                const formData = new FormData();
                formData.append('title', comparisonTitle.value);
                formData.append('procurement_id', parseInt(selectedRequestId.value, 10));

                // If editing, add the comparison ID to the form data
                if (isEditMode) {
                    formData.append('_method', 'PUT'); // Laravel method spoofing for PUT requests
                }

                // Set submitting flag and disable button
                isSubmitting = true;

                // Disable submit button during submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    ${isEditMode ? 'Menyimpan...' : 'Mengirim...'}
                `;

                // Determine the endpoint based on whether we're creating or editing
                const endpoint = isEditMode
                    ? `{{ url('procurement/price-comparison') }}/${comparisonId}`
                    : '{{ route('procurement.store-price-comparison') }}';

                // Submit the form via AJAX
                fetch(endpoint, {
                    method: isEditMode ? 'POST' : 'POST', // Using POST with _method for PUT
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
                        showSweetAlert(data.message || (isEditMode ? 'Perbandingan harga berhasil diperbarui!' : 'Perbandingan harga berhasil dibuat!'));

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
                        submitBtn.innerHTML = isEditMode ? 'SIMPAN' : 'KIRIM';

                        // Handle structured errors
                        if (data.errors) {
                            let errorMessage = '';

                            // Check if errors is an array of objects with path and message
                            if (Array.isArray(data.errors)) {
                                // Create an HTML list of error messages
                                errorMessage = '<ul class="text-left">';
                                data.errors.forEach(error => {
                                    errorMessage += `<li>${error.message || error}</li>`;
                                });
                                errorMessage += '</ul>';
                            }
                            // Check if errors has a general key (common pattern)
                            else if (data.errors.general) {
                                errorMessage = data.errors.general;
                            }
                            // Object with error keys
                            else {
                                errorMessage = '<ul class="text-left">';
                                Object.keys(data.errors).forEach(key => {
                                    const errorItems = Array.isArray(data.errors[key]) ? data.errors[key] : [data.errors[key]];
                                    errorItems.forEach(item => {
                                        errorMessage += `<li>${item}</li>`;
                                    });
                                });
                                errorMessage += '</ul>';
                            }

                            showSweetAlert(errorMessage, 'error');
                        } else {
                            showSweetAlert(data.message || `Terjadi kesalahan saat ${isEditMode ? 'memperbarui' : 'membuat'} perbandingan harga`, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error(`Error ${isEditMode ? 'updating' : 'creating'} price comparison:`, error);

                    // Reset flag and button on error
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = isEditMode ? 'SIMPAN' : 'KIRIM';

                    // Handle structured errors similar to above
                    if (error.errors) {
                        let errorMessage = '';

                        // Check if errors is an array of objects with path and message
                        if (Array.isArray(error.errors)) {
                            // Create an HTML list of error messages
                            errorMessage = '<ul class="text-left">';
                            error.errors.forEach(err => {
                                errorMessage += `<li>${err.message || err}</li>`;
                            });
                            errorMessage += '</ul>';
                        }
                        // Check if errors has a general key (common pattern)
                        else if (error.errors.general) {
                            errorMessage = error.errors.general;
                        }
                        // Object with error keys
                        else {
                            errorMessage = '<ul class="text-left">';
                            Object.keys(error.errors).forEach(key => {
                                const errorItems = Array.isArray(error.errors[key]) ? error.errors[key] : [error.errors[key]];
                                errorItems.forEach(item => {
                                    errorMessage += `<li>${item}</li>`;
                                });
                            });
                            errorMessage += '</ul>';
                        }

                        showSweetAlert(errorMessage, 'error');
                    } else {
                        showSweetAlert(error.message || `Terjadi kesalahan saat ${isEditMode ? 'memperbarui' : 'membuat'} perbandingan harga`, 'error');
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
