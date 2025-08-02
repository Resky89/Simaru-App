@extends('Layout.app')

@section('title', 'Receipt Form')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <!-- Toast container for notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-4"></div>

    @if(hasPermission('receipt:create'))
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
                        <input type="text" id="receipt_date" name="receipt_date" value="{{ date('Y-m-d') }}"
                            placeholder="Pilih Tanggal"
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
                                <div id="users_loading" class="p-2 text-gray-500 text-center">
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Memuat Pengguna...</span>
                                </div>
                                <ul id="users_list" class="py-1"></ul>
                                <!-- Load more indicator -->
                                <div id="users_load_more" class="p-2 text-gray-500 text-center hidden">
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Memuat lebih banyak...</span>
                                </div>
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown for search results -->
                        <div id="po_dropdown"
                            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                            <!-- Loading indicator -->
                            <div id="po_loading" class="p-2 text-gray-500 text-center">
                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Memuat Pemesanan...</span>
                            </div>
                            <ul id="po_list" class="py-1"></ul>
                            <!-- Load more indicator -->
                            <div id="po_load_more" class="p-2 text-gray-500 text-center hidden">
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

                <!-- Purchase Order Details (Initially Hidden) -->
                <div id="poDetails" class="border border-[#CCCCCC] rounded-lg p-4 bg-[#F9FAFB] mt-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column - Order Information -->
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-[#666666]">Informasi Pemesanan</h2>
                            <table class="w-full">
                                <tbody>
                                    <!-- PO Number -->
                                    <tr>
                                        <td class="py-1 align-top w-48 font-medium text-[#666666]">Nomor Pemesanan</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayPoCode"></span></td>
                                    </tr>
                                    <!-- Comparison ID -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Nomor Penawaran</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayComparisonCode"></span>
                                        </td>
                                    </tr>
                                    <!-- PO Date -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Tanggal Pemesanan</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayPoDate"></span></td>
                                    </tr>
                                    <!-- Creator -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Dibuat oleh</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayCreator"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Right Column - Vendor Information -->
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-[#666666]">Informasi Vendor</h2>
                            <table class="w-full">
                                <tbody>
                                    <!-- Vendor -->
                                    <tr>
                                        <td class="py-1 align-top w-48 font-medium text-[#666666]">Nama Vendor</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayVendor"></span></td>
                                    </tr>
                                    <!-- PIC -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Penanggung Jawab</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayPic"></span></td>
                                    </tr>
                                    <!-- PIC Contact -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Nomor Telepon</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayPicContact"></span></td>
                                    </tr>
                                    <!-- Email (if available) -->
                                    <tr>
                                        <td class="py-1 align-top font-medium text-[#666666]">Email</td>
                                        <td class="py-1 align-top text-[#666666]">: <span id="displayEmail"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4 mt-6">
                        <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">NAMA ASET</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">JUMLAH</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-right">HARGA SATUAN</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-right">TOTAL</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">CATATAN</th>
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
    @else
        <!-- Permission Denied Message -->
        <div class="p-4 md:p-7 bg-base-100 rounded-lg">
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
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Flatpickr with Indonesian locale and configuration
            flatpickr.localize(flatpickr.l10ns.id);

            // Define Indonesian locale
            const indonesianLocale = {
                weekdays: {
                    longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                },
                months: {
                    longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                },
                firstDayOfWeek: 1,
                rangeSeparator: " sampai ",
                weekAbbreviation: "Minggu",
                scrollTitle: "Gulir untuk menambah",
                toggleTitle: "Klik untuk beralih",
                time_24hr: true
            };

            // Initialize receipt date picker with Indonesian format
            const receiptDatePicker = flatpickr("#receipt_date", {
                locale: 'id',
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                static: true,
                disableMobile: true,
                allowInput: false,
                clickOpens: true,
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.style.width = "100%";
                        instance.altInput.style.display = "block";

                        const parentWrapper = instance.altInput.closest('.flatpickr-wrapper');
                        if (parentWrapper) {
                            parentWrapper.style.width = "100%";
                            parentWrapper.style.display = "block";
                        }

                        instance.altInput.className = document.getElementById('receipt_date').className;
                    }

                    if (selectedDates && selectedDates.length > 0) {
                        const date = selectedDates[0];
                        const day = date.getDate();
                        const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        const month = monthsInIndonesian[date.getMonth()];
                        const year = date.getFullYear();

                        if (instance.altInput) {
                            instance.altInput.value = `${day} ${month} ${year}`;
                        }
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates && selectedDates.length > 0) {
                        const date = selectedDates[0];
                        const day = date.getDate();
                        const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        const month = monthsInIndonesian[date.getMonth()];
                        const year = date.getFullYear();

                        if (instance.altInput) {
                            instance.altInput.value = `${day} ${month} ${year}`;
                        }
                    }
                },
                formatDate: (date, format) => {
                    if (format === "Y-m-d") {
                        const localDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                        const year = localDate.getFullYear();
                        const month = String(localDate.getMonth() + 1).padStart(2, '0');
                        const day = String(localDate.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }

                    if (format === "j F Y") {
                        const day = date.getDate();
                        const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        const month = monthsInIndonesian[date.getMonth()];
                        const year = date.getFullYear();
                        return `${day} ${month} ${year}`;
                    }

                    return flatpickr.formatDate(date, format);
                },
                parseDate: (datestr, format) => {
                    if (format === "Y-m-d") {
                        const [year, month, day] = datestr.split("-").map(Number);
                        return new Date(year, month - 1, day);
                    }
                    return flatpickr.parseDate(datestr, format);
                }
            });

            const form = document.getElementById('receiptForm');
            const searchBtn = document.getElementById('searchBtn');
            const poDetails = document.getElementById('poDetails');
            const purchaseOrderNumber = document.getElementById('purchaseOrderNumber');
            const selectedPoId = document.getElementById('selected_po_id');
            const poDropdown = document.getElementById('po_dropdown');
            const poList = document.getElementById('po_list');
            const poLoading = document.getElementById('po_loading');
            const receivedByInput = document.getElementById('receivedByInput');
            const receivedByField = document.getElementById('received_by');
            const usersDropdown = document.getElementById('users_dropdown');
            const usersList = document.getElementById('users_list');
            const usersLoading = document.getElementById('users_loading');

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
                return showSweetAlert(message, type);
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

            // Helper function untuk membuat dropdown items
            function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                const li = document.createElement('li');
                li.className = className;
                li.textContent = text;
                return li;
            }

            // Event listeners untuk pencarian purchase order
            purchaseOrderNumber.addEventListener('focus', function () {
                poDropdown.classList.remove('hidden');
                if (poList.children.length === 0) {
                    // Reset pagination
                    poList.dataset.page = "1";
                    poList.dataset.hasMoreData = "true";
                    loadPurchaseOrders('');
                }
            });

            document.addEventListener('click', function (e) {
                if (!purchaseOrderNumber.contains(e.target) && !poDropdown.contains(e.target) && !searchBtn.contains(e.target)) {
                    poDropdown.classList.add('hidden');
                }
            });

            // Add scroll event listener for lazy loading
            poDropdown.addEventListener('scroll', function () {
                // Check if we're already loading or if there's no more data
                if (poList.dataset.loading === "true" || poList.dataset.hasMoreData === "false") return;

                const { scrollTop, scrollHeight, clientHeight } = poDropdown;
                // When user is near the bottom (20px threshold)
                if (scrollTop + clientHeight >= scrollHeight - 20) {
                    loadPurchaseOrders(poList.dataset.searchTerm || '');
                }
            });

            const debouncedSearch = debounce(function (e) {
                const searchTerm = e.target.value.trim();
                // Reset pagination when searching
                poList.dataset.page = "1";
                poList.dataset.hasMoreData = "true";
                loadPurchaseOrders(searchTerm);

                // Show dropdown saat mengetik
                poDropdown.classList.remove('hidden');
            }, 300);

            purchaseOrderNumber.addEventListener('input', debouncedSearch);

            // Enter key handler untuk pencarian
            purchaseOrderNumber.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const searchTerm = purchaseOrderNumber.value.trim();
                    if (searchTerm) {
                        poDropdown.classList.remove('hidden');
                        // Reset pagination
                        poList.dataset.page = "1";
                        poList.dataset.hasMoreData = "true";
                        loadPurchaseOrders(searchTerm);
                    }
                }
            });

            async function loadPurchaseOrders(searchTerm) {
                // Setup for lazy loading
                let page = poList.dataset.page ? parseInt(poList.dataset.page) : 1;
                let isLoading = poList.dataset.loading === "true";
                let hasMoreData = poList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || poList.dataset.searchTerm !== searchTerm;
                const loadMoreIndicator = document.getElementById('po_load_more');

                // Reset page to 1 if search term changed
                if (poList.dataset.searchTerm !== searchTerm) {
                    page = 1;
                    poList.dataset.page = "1";
                    resetList = true;
                }

                // Save current search term
                poList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                poList.dataset.loading = "true";

                // Use different loading indicators based on whether we're resetting or loading more
                if (resetList) {
                    if (poLoading) poLoading.classList.remove('hidden');
                    poList.innerHTML = '';
                } else {
                    if (loadMoreIndicator) loadMoreIndicator.classList.remove('hidden');
                }

                try {
                    const response = await fetch(`/procurement/purchase-order?search=${encodeURIComponent(searchTerm || '')}&page=${page}&limit=20`, {
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

                    const receiptsResponse = await fetch(`/procurement/receipt?json=true&limit=1000&search=${encodeURIComponent(searchTerm || '')}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!receiptsResponse.ok) {
                        throw new Error('Gagal mengambil data penerimaan');
                    }

                    const receiptsResult = await receiptsResponse.json();

                    const purchaseOrdersWithReceipts = new Set();

                    let receipts = [];
                    if (receiptsResult && receiptsResult.success === true && Array.isArray(receiptsResult.data)) {
                        receipts = receiptsResult.data;
                    } else if (receiptsResult && Array.isArray(receiptsResult.receipts)) {
                        receipts = receiptsResult.receipts;
                    }

                    if (receipts && receipts.length > 0) {
                        receipts.forEach(receipt => {
                            if (receipt && receipt.purchase_order_id) {
                                purchaseOrdersWithReceipts.add(receipt.purchase_order_id);
                            }
                        });
                    }

                    const filteredPurchaseOrders = purchaseOrders.filter(po =>
                        !purchaseOrdersWithReceipts.has(po.purchase_order_id)
                    );

                    // Check if we have more data to load
                    hasMoreData = filteredPurchaseOrders.length === 20;

                    // Save next page number and has more data state
                    poList.dataset.page = page + 1;
                    poList.dataset.hasMoreData = hasMoreData.toString();

                    if (filteredPurchaseOrders.length === 0 && poList.children.length === 0) {
                        poList.appendChild(createDropdownItem('Tidak ada purchase order yang tersedia untuk penerimaan', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        filteredPurchaseOrders.forEach(po => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'po-item';

                            const codeSpan = document.createElement('div');
                            codeSpan.className = 'code text-black font-medium';
                            codeSpan.textContent = po.purchase_order_code || '';
                            itemContainer.appendChild(codeSpan);

                            if (po.vendor_name) {
                                const vendorSpan = document.createElement('div');
                                vendorSpan.className = 'vendor text-gray-500 text-sm';
                                vendorSpan.textContent = po.vendor_name;
                                itemContainer.appendChild(vendorSpan);
                            }

                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', po.purchase_order_id);
                            li.setAttribute('data-code', po.purchase_order_code);
                            li.setAttribute('data-vendor', po.vendor_name || '');
                            li.setAttribute('data-user', po.creator_name || 'Staff');
                            li.setAttribute('data-date', po.created_at || '');
                            li.addEventListener('click', function () {
                                selectedPoId.value = this.getAttribute('data-id');
                                purchaseOrderNumber.value = this.getAttribute('data-code');
                                poDropdown.classList.add('hidden');
                            });

                            poList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading purchase orders:', error);
                    if (poList.children.length === 0) {
                        poList.appendChild(createDropdownItem('Gagal memuat daftar purchase order', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    poList.dataset.loading = "false";
                    if (poLoading) poLoading.classList.add('hidden');
                    if (loadMoreIndicator) loadMoreIndicator.classList.add('hidden');
                }
            }

            if (searchBtn) {
                searchBtn.addEventListener('click', function () {
                    const poCode = purchaseOrderNumber.value.trim();

                    if (!poCode) {
                        showToast('Mohon masukkan nomor purchase order', 'error');
                        return;
                    }

                    if (selectedPoId.value && isNaN(parseInt(selectedPoId.value, 10))) {
                        showToast('ID purchase order tidak valid', 'error');
                        return;
                    }

                    // Show dropdown untuk hasil pencarian
                    poDropdown.classList.remove('hidden');

                    // Trigger pencarian dengan term saat ini
                    if (!selectedPoId.value) {
                        // Reset pagination dan load data
                        poList.dataset.page = "1";
                        poList.dataset.hasMoreData = "true";
                        loadPurchaseOrders(poCode);
                    }

                    const originalBtnText = searchBtn.innerHTML;
                    searchBtn.disabled = true;
                    searchBtn.innerHTML = `
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    `;

                    const poId = selectedPoId.value || null;

                    if (poId) {
                        fetchPurchaseOrderDetails(parseInt(poId, 10))
                            .finally(() => {
                                searchBtn.disabled = false;
                                searchBtn.innerHTML = originalBtnText;
                            });
                    } else {
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
                                    const exactMatch = result.data.find(item =>
                                        item.purchase_order_code &&
                                        item.purchase_order_code.toLowerCase() === poCode.toLowerCase());

                                    if (exactMatch) {
                                        return fetch(`/procurement/receipt?json=true&limit=1000&search=${encodeURIComponent(poCode)}`, {
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
                                                const purchaseOrdersWithReceipts = new Set();

                                                let receipts = [];
                                                if (receiptsResult && receiptsResult.success === true && Array.isArray(receiptsResult.data)) {
                                                    receipts = receiptsResult.data;
                                                } else if (receiptsResult && Array.isArray(receiptsResult.receipts)) {
                                                    receipts = receiptsResult.receipts;
                                                }

                                                if (receipts && receipts.length > 0) {
                                                    receipts.forEach(receipt => {
                                                        if (receipt && receipt.purchase_order_id) {
                                                            purchaseOrdersWithReceipts.add(receipt.purchase_order_id);
                                                        }
                                                    });
                                                }

                                                if (purchaseOrdersWithReceipts.has(exactMatch.purchase_order_id)) {
                                                    throw new Error('Purchase order ini sudah memiliki penerimaan');
                                                }

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

            async function fetchPurchaseOrderDetails(poId) {
                try {
                    document.getElementById('assetListTableBody').innerHTML = '';

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

                    // Display PO information
                    document.getElementById('displayPoCode').textContent = purchaseOrder.purchase_order_code || '';
                    document.getElementById('displayComparisonCode').textContent = purchaseOrder.comparison_code || 'N/A';
                    document.getElementById('displayCreator').textContent = purchaseOrder.creator_employee_name || 'N/A';

                    // Format and display date
                    let displayDate = purchaseOrder.created_at || '';
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
                    document.getElementById('displayPoDate').textContent = displayDate;

                    // Display vendor information
                    if (purchaseOrder.vendor && typeof purchaseOrder.vendor === 'object') {
                        document.getElementById('displayVendor').textContent = purchaseOrder.vendor.vendor_name || 'N/A';
                        document.getElementById('displayPic').textContent = purchaseOrder.vendor.contact_person || 'N/A';
                        document.getElementById('displayPicContact').textContent = purchaseOrder.vendor.phone_number || 'N/A';
                        document.getElementById('displayEmail').textContent = purchaseOrder.vendor.email || 'N/A';
                    } else {
                        document.getElementById('displayVendor').textContent = purchaseOrder.vendor_name || 'N/A';
                        document.getElementById('displayPic').textContent = 'N/A';
                        document.getElementById('displayPicContact').textContent = 'N/A';
                        document.getElementById('displayEmail').textContent = 'N/A';
                    }

                    populateAssetList(purchaseOrder.items || []);

                    poDetails.classList.remove('hidden');

                } catch (error) {
                    console.error('Error fetching purchase order details:', error);
                    showToast('Gagal memuat detail purchase order: ' + error.message, 'error');

                    poDetails.classList.add('hidden');
                }
            }

            function populateAssetList(items) {
                const tableBody = document.getElementById('assetListTableBody');
                tableBody.innerHTML = '';

                if (!items || items.length === 0) {
                    const row = document.createElement('tr');
                    row.className = 'border-t border-[#EEF1F4]';

                    const cell = document.createElement('td');
                    cell.className = 'p-3 text-xs text-[#666666] text-center';
                    cell.colSpan = 5;
                    cell.textContent = 'Tidak ada item ditemukan untuk purchase order ini';

                    row.appendChild(cell);
                    tableBody.appendChild(row);
                } else {
                    let grandTotal = 0;

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

                        // Unit price cell
                        const unitPriceCell = document.createElement('td');
                        unitPriceCell.className = 'p-3 text-sm text-right text-[#666666]';
                        const unitPrice = item.unit_price ? parseFloat(item.unit_price) : 0;
                        unitPriceCell.textContent = unitPrice.toLocaleString('id-ID');
                        row.appendChild(unitPriceCell);

                        // Total price cell
                        const totalPriceCell = document.createElement('td');
                        totalPriceCell.className = 'p-3 text-sm text-right text-[#666666]';
                        const totalPrice = item.total_price ? parseFloat(item.total_price) : 0;
                        totalPriceCell.textContent = totalPrice.toLocaleString('id-ID');
                        row.appendChild(totalPriceCell);

                        grandTotal += totalPrice;

                        // Notes cell
                        const notesCell = document.createElement('td');
                        notesCell.className = 'p-3 text-sm text-[#666666]';

                        const notesInput = document.createElement('input');
                        notesInput.type = 'text';
                        notesInput.placeholder = 'Tambahkan catatan';
                        notesInput.className = 'w-full p-2 border border-[#CCCCCC] rounded-md text-[#666666]';
                        notesInput.name = `item_notes[${item.purchase_order_item_id}]`;
                        notesInput.dataset.item_id = item.purchase_order_item_id;

                        notesCell.appendChild(notesInput);
                        row.appendChild(notesCell);

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'items[]';
                        hiddenInput.value = item.purchase_order_item_id;
                        row.appendChild(hiddenInput);

                        tableBody.appendChild(row);
                    });

                    // Add grand total row
                    const totalRow = document.createElement('tr');
                    totalRow.className = 'border-t border-[#EEF1F4]';

                    const totalLabelCell = document.createElement('td');
                    totalLabelCell.colSpan = 3;
                    totalLabelCell.className = 'p-3 text-sm font-medium text-right text-[#666666]';
                    totalLabelCell.textContent = 'Total Keseluruhan';
                    totalRow.appendChild(totalLabelCell);

                    const grandTotalCell = document.createElement('td');
                    grandTotalCell.className = 'p-3 text-sm text-right text-[#666666] font-medium';
                    grandTotalCell.textContent = grandTotal.toLocaleString('id-ID');
                    totalRow.appendChild(grandTotalCell);

                    const emptyCell = document.createElement('td');
                    totalRow.appendChild(emptyCell);

                    tableBody.appendChild(totalRow);
                }
            }

            if (form) {
                let isSubmitting = false;
                let isNavigatingAway = false;
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (isSubmitting) {
                        return;
                    }

                    let isValid = true;

                    if (!selectedPoId.value) {
                        showSweetAlert('Mohon pilih purchase order terlebih dahulu', 'error');
                        return;
                    }

                    const receiptDateField = document.getElementById('receipt_date');
                    if (!receiptDateField.value) {
                        receiptDateField.classList.add('border-red-500');
                        const errorElement = receiptDateField.closest('.form-control').querySelector('.error-message');
                        if (errorElement) errorElement.classList.remove('hidden');
                        isValid = false;
                    }

                    const deliveredByField = document.getElementById('delivered_by');

                    if (!validateReceivedBy()) {
                        receivedByInput.classList.add('border-red-500');
                        const errorElement = receivedByInput.closest('.form-control').querySelector('.error-message');
                        if (errorElement) errorElement.classList.remove('hidden');
                        isValid = false;
                    }

                    if (!isValid) {
                        showSweetAlert('Mohon lengkapi semua field yang wajib diisi', 'error');
                        return;
                    }

                    receivedByField.value = parseInt(receivedByField.value, 10);
                    if (isNaN(receivedByField.value)) {
                        showToast('ID pegawai tidak valid', 'error');
                        return;
                    }

                    const items = [];
                    const itemInputs = document.querySelectorAll('input[name="items[]"]');

                    itemInputs.forEach(input => {
                        const item_id = input.value;
                        const notes_input = document.querySelector(`input[data-item_id="${item_id}"]`);

                        const itemData = {
                            purchase_order_item_id: parseInt(item_id)
                        };

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

                    const receivedByValue = parseInt(receivedByField.value, 10);
                    if (isNaN(receivedByValue)) {
                        showToast('ID penerima tidak valid', 'error');
                        return;
                    }

                    const receiptData = {
                        purchase_order_id: parseInt(selectedPoId.value),
                        receipt_date: document.getElementById('receipt_date').value,
                        received_by: receivedByValue
                    };

                    const deliveredByValue = document.getElementById('delivered_by').value.trim();
                    if (deliveredByValue) {
                        receiptData.delivered_by = deliveredByValue;
                    }

                    const notesValue = document.getElementById('notesField').value.trim();
                    if (notesValue) {
                        receiptData.notes = notesValue;
                    }

                    receiptData.items = items;
                    isSubmitting = true;

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        MENYIMPAN...
                                    `;

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
                                showSweetAlert(
                                    data.message || 'Penerimaan barang berhasil dibuat!',
                                    'success',
                                    {
                                        timer: 1500,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            isNavigatingAway = true;
                                        },
                                        willClose: () => {
                                            window.location.href = "{{ route('procurement.receipt') }}";
                                        }
                                    }
                                );
                            } else {
                                isSubmitting = false;
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                if (data.errors) {
                                    const errorData = data.errors;

                                    let errorMessage = 'Terjadi kesalahan saat memproses permintaan Anda:';
                                    let errorList = [];

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

                            isSubmitting = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;

                            showSweetAlert('Terjadi kesalahan saat membuat penerimaan barang', 'error');
                        });
                });
            }

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

            receivedByInput.addEventListener('input', function () {
                receivedByInput.classList.remove('border-red-500');
                const errorElement = this.closest('.form-control').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.getElementById('receipt_date').addEventListener('change', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.form-control').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            receivedByInput.addEventListener('focus', function () {
                usersDropdown.classList.remove('hidden');

                if (usersList.children.length === 0) {
                    usersList.appendChild(createDropdownItem('Mulai mengetik untuk mencari pengguna', 'px-4 py-2 text-gray-500 italic'));
                    // Load users with empty search
                    loadUsers('');
                }
            });

            document.addEventListener('click', function (e) {
                if (!receivedByInput.contains(e.target) && !usersDropdown.contains(e.target)) {
                    usersDropdown.classList.add('hidden');
                }
            });

            // Helper function to create dropdown items
            function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                const li = document.createElement('li');
                li.className = className;
                li.textContent = text;
                return li;
            }

            const debouncedUserSearch = debounce(function (e) {
                const searchTerm = e.target.value.trim();
                loadUsers(searchTerm);
                usersDropdown.classList.remove('hidden');
            }, 300);

            receivedByInput.addEventListener('input', debouncedUserSearch);

            receivedByInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const searchTerm = receivedByInput.value.trim();
                    usersDropdown.classList.remove('hidden');
                    loadUsers(searchTerm);
                }
            });

            usersDropdown.addEventListener('scroll', function () {
                if (usersList.dataset.loading === "true" || usersList.dataset.hasMoreData === "false") return;

                const { scrollTop, scrollHeight, clientHeight } = usersDropdown;
                if (scrollTop + clientHeight >= scrollHeight - 20) {
                    loadUsers(usersList.dataset.searchTerm || '');
                }
            });

            async function loadUsers(searchTerm) {
                let page = usersList.dataset.page ? parseInt(usersList.dataset.page) : 1;
                let isLoading = usersList.dataset.loading === "true";
                let hasMoreData = usersList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || usersList.dataset.searchTerm !== searchTerm;
                const loadMoreIndicator = document.getElementById('users_load_more');

                usersList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                usersList.dataset.loading = "true";

                if (resetList) {
                    if (usersLoading) usersLoading.classList.remove('hidden');
                    usersList.innerHTML = '';
                } else {
                    if (loadMoreIndicator) loadMoreIndicator.classList.remove('hidden');
                }

                try {
                    const response = await fetch(`{{ route('user') }}?search=${encodeURIComponent(searchTerm || '')}&status=active&page=${page}&limit=20`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil daftar pengguna');
                    }

                    const result = await response.json();
                    let users = [];

                    if (result.users && Array.isArray(result.users)) {
                        users = result.users;
                    } else if (result.data && Array.isArray(result.data)) {
                        users = result.data;
                    } else if (Array.isArray(result)) {
                        users = result;
                    }

                    hasMoreData = users.length === 20;

                    usersList.dataset.page = page + 1;
                    usersList.dataset.hasMoreData = hasMoreData.toString();

                    if (users.length === 0 && usersList.children.length === 0) {
                        usersList.appendChild(createDropdownItem('Tidak ada pengguna ditemukan', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        users.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'user-item';

                            let displayName = '';
                            let displayId = '';

                            if (user.employee_name) {
                                displayName = user.employee_name;
                            } else {
                                displayName = user.name || `User ID: ${user.user_id || user.id}`;
                            }

                            const nameSpan = document.createElement('div');
                            nameSpan.className = 'name text-black font-medium';
                            nameSpan.textContent = displayName;
                            itemContainer.appendChild(nameSpan);

                            if (displayId) {
                                const idSpan = document.createElement('div');
                                idSpan.className = 'code text-gray-500 text-sm';
                                idSpan.textContent = displayId;
                                itemContainer.appendChild(idSpan);
                            }

                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', user.user_id || user.id || '');
                            li.setAttribute('data-employee-name', user.employee_name || '');
                            li.setAttribute('data-name', displayName);
                            if (displayId) {
                                li.setAttribute('data-display', `${displayId} - ${displayName}`);
                            } else {
                                li.setAttribute('data-display', displayName);
                            }

                            li.addEventListener('click', function () {
                                receivedByField.value = this.getAttribute('data-id');
                                receivedByInput.value = this.getAttribute('data-display') || this.getAttribute('data-name');
                                usersDropdown.classList.add('hidden');
                                receivedByInput.classList.remove('border-red-500');
                                const errorElement = receivedByInput.closest('.form-control').querySelector('.error-message');
                                if (errorElement) errorElement.classList.add('hidden');
                            });

                            usersList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading users:', error);

                    if (usersList.children.length === 0) {
                        usersList.appendChild(createDropdownItem('Gagal memuat daftar pengguna: ' + (error.message || 'Unknown error'), 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    usersList.dataset.loading = "false";
                    if (usersLoading) usersLoading.classList.add('hidden');
                    if (loadMoreIndicator) loadMoreIndicator.classList.add('hidden');
                }
            }

            document.head.insertAdjacentHTML('beforeend', `
                        <style>
                            @keyframes slideInRight {
                                from { transform: translateX(100%); }
                                to { transform: translateX(0); }
                            }
                            .animate-slide-in-right {
                                animation: slideInRight 0.3s ease-out forwards;
                            }

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

                            /* User dropdown styles */
                            .user-item {
                                display: flex;
                                flex-direction: column;
                            }
                            .user-item .name {
                                font-weight: 500;
                            }
                            .user-item .code {
                                font-size: 0.8rem;
                                color: #666;
                            }

                            /* Animation for new items */
                            @keyframes fadeIn {
                                from { opacity: 0; transform: translateY(5px); }
                                to { opacity: 1; transform: translateY(0); }
                            }
                            #po_list li {
                                animation: fadeIn 0.2s ease-out forwards;
                            }

                            /* Purchase Order dropdown styles */
                            .po-item {
                                display: flex;
                                flex-direction: column;
                            }
                            .po-item .code {
                                font-weight: 500;
                            }
                            .po-item .vendor {
                                font-size: 0.8rem;
                                color: #666;
                            }
                            #po_list li {
                                animation: fadeIn 0.2s ease-out forwards;
                            }
                        </style>
                    `);

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
                            window.location.href = '{{ route("procurement.receipt") }}';
                        }
                    });
                } else {
                    window.location.href = '{{ route("procurement.receipt") }}';
                }
            });
        });
    </script>
@endpush
