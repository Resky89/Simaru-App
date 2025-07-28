<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-[#213268]">PENYUSUTAN</h2>
        <div class="flex items-center gap-4">
            <div class="flex items-center">
                <span class="text-sm text-gray-600 mr-2">Nilai</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="percentageToggle" class="sr-only peer" checked>
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#213268] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]">
                    </div>
                    <span class="ml-2 text-sm text-gray-600">Persen</span>
                </label>
            </div>
            @if(hasPermission('asset:depreciation:edit'))
                <button id="updateDepreciationBtn"
                    class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    PENGATURAN
                </button>
            @endif
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="depreciationLoadingIndicator" class="flex justify-center items-center py-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#213268]"></div>
        <span class="ml-2 text-gray-600">Memuat data penyusutan...</span>
    </div>

    <!-- Error message container -->
    <div id="depreciationErrorMessage"
        class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    </div>

    <!-- Content sections -->
    <div id="contentSections" class="hidden">
        <!-- Top Asset Depreciation Table -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Pengadaan</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Total Biaya</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nilai Sisa</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Usia Asset (Bulan)</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Metode Penyusutan</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nilai Saat Ini</th>
                    </tr>
                </thead>
                <tbody id="depreciationSummary">
                    <tr>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- No Depreciation Data Message -->
        <div id="noDepreciationData" class="hidden">
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-8 rounded-lg mb-8 text-center">
                <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium mb-2">Asset Ini Belum Memiliki Data Penyusutan</h3>
                <p class="text-blue-600">Silakan gunakan tombol Pengaturan untuk menambahkan data penyusutan</p>
            </div>
        </div>

        <!-- Depreciation Chart -->
        <div id="depreciationChartContainer" class="bg-white p-4 md:p-6 rounded-lg shadow-sm mb-8">
            <h3 class="text-center text-lg font-semibold text-[#213268] mb-6">Penyusutan Bulanan</h3>
            <div class="h-64 w-full">
                <canvas id="depreciationChart"></canvas>
            </div>
        </div>

        <!-- Bottom Depreciation Details Table -->
        <div id="depreciationDetailsContainer" class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">#</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Bulan</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Pengeluaran Penyusutan</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Penyusutan Akumulasi di
                            Akhir Bulan</th>
                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nilai Buku di Akhir Bulan
                        </th>
                    </tr>
                </thead>
                <tbody id="monthlyDepreciationData">
                    <tr>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Depreciation Modal -->
    @if(hasPermission('asset:depreciation:edit'))
        <div id="updateDepreciationModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="updateDepreciationModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Pengaturan Penyusutan</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="updateDepreciationModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="updateDepreciationForm"
                                action="{{ route('asset-depreciation.update', ['assetId' => $asset['asset_id'] ?? '']) }}"
                                method="POST" data-no-loading>
                                @csrf
                                @method('PUT')
                                <div class="space-y-6">
                                    <!-- Depreciation Method -->
                                    <div>
                                        <label for="depreciation_method"
                                            class="block text-sm font-medium text-gray-700 mb-1">Metode Penyusutan <span
                                                class="text-red-500">*</span></label>
                                        <select id="depreciation_method" name="depreciation_method"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                            required>
                                            <option value="">Pilih Metode Penyusutan</option>
                                            <option value="Straight Line">Garis Lurus (Straight Line)</option>
                                            <option value="Declining Balance">Saldo Menurun (Declining Balance)</option>
                                            <option value="Double Declining Balance">Saldo Menurun Ganda (Double Declining
                                                Balance)</option>
                                            <option value="150% Declining Balance">Saldo Menurun 150% (150% Declining
                                                Balance)</option>
                                            <option value="Sum of the Years Digits">Jumlah Digit Tahun (Sum of Year's
                                                Digits)</option>
                                        </select>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus
                                            dipilih</div>
                                    </div>

                                    <!-- Acquisition Cost and Salvage Value side by side -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="acquisition_cost"
                                                class="block text-sm font-medium text-gray-700 mb-1">Biaya Pengadaan <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="acquisition_cost" name="acquisition_cost" placeholder="0"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                                required onkeyup="formatCurrency(this)"
                                                onblur="formatCurrency(this, 'blur')">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pengadaan
                                                harus diisi</div>
                                        </div>
                                        <div>
                                            <label for="salvage_value"
                                                class="block text-sm font-medium text-gray-700 mb-1">Nilai Sisa <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="salvage_value" name="salvage_value" placeholder="0"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                                required onkeyup="formatCurrency(this)"
                                                onblur="formatCurrency(this, 'blur')">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus
                                                diisi</div>
                                        </div>
                                    </div>

                                    <!-- Asset Life and Date Acquired side by side -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="asset_life_months"
                                                class="block text-sm font-medium text-gray-700 mb-1">Usia Asset (bulan)
                                                <span class="text-red-500">*</span></label>
                                            <input type="number" id="asset_life_months" name="asset_life_months" min="1"
                                                max="360"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                                required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Usia asset harus
                                                diisi</div>
                                        </div>
                                        <div>
                                            <label for="date_acquired"
                                                class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengadaan <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="date_acquired" name="date_acquired"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                                required placeholder="Pilih Tanggal">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pengadaan
                                                harus diisi</div>
                                        </div>
                                    </div>

                                    <!-- Error message container -->
                                    <div id="update-form-error"
                                        class="hidden text-red-500 text-sm p-2 bg-red-50 rounded-md mt-2 mb-4"></div>

                                    <!-- Form Actions -->
                                    <div class="flex justify-end">
                                        <button type="button" id="updateDepreciationSubmitBtn"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#162249]">
                                            <span class="flex items-center justify-center">
                                                Perbarui Penyusutan
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Toast Notification Container -->
<div id="depreciation-toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const DepreciationSystem = {
                initialized: false,
                assetId: {{ $asset['asset_id'] ?? 'null' }},
                apiBaseUrl: "{{ config('app.api_url', '') }}",
                chart: null,
                currentDepreciation: null,
                isPercentageView: true,
                originalChartData: null,
                hasEditPermission: {{ hasPermission('asset:depreciation:edit') ? 'true' : 'false' }},
                flatpickrInstance: null,

                init() {
                    if (this.initialized) return;

                    if (!this.assetId) {
                        console.error('Asset ID tidak tersedia');
                        this.showError('Asset ID tidak tersedia');
                        return;
                    }

                    this.setupModalHelpers();
                    this.setupEventListeners();
                    this.setupFormInputs();
                    this.initFlatpickr();
                    this.loadDepreciationData();
                    this.initialized = true;
                },

                // Add Flatpickr initialization for date picker
                initFlatpickr() {
                    // Dynamically load Flatpickr if not already available
                    if (typeof flatpickr === 'undefined') {
                        // Create link for CSS
                        const cssLink = document.createElement('link');
                        cssLink.rel = 'stylesheet';
                        cssLink.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
                        document.head.appendChild(cssLink);

                        // Create script for Flatpickr core
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                        script.onload = () => {
                            this.setupDatePicker();
                        };
                        document.head.appendChild(script);
                    } else {
                        this.setupDatePicker();
                    }
                },

                setupDatePicker() {
                    const dateAcquiredInput = document.getElementById('date_acquired');
                    if (dateAcquiredInput) {
                        // Define manual Indonesian locale untuk memastikan tampilan tanggal dalam bahasa Indonesia
                        const indonesianLocale = {
                            weekdays: {
                                shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                                longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                            },
                            months: {
                                shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
                                longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                            },
                            firstDayOfWeek: 1,
                            rangeSeparator: " sampai ",
                            weekAbbreviation: "Minggu",
                            scrollTitle: "Gulir untuk menambah",
                            toggleTitle: "Klik untuk beralih",
                            time_24hr: true,
                        };

                        // Daftarkan locale kustom ke flatpickr
                        if (flatpickr.l10ns) {
                            flatpickr.l10ns.id = indonesianLocale;
                        }

                        this.flatpickrInstance = flatpickr(dateAcquiredInput, {
                            locale: 'id', // Gunakan ID locale yang sudah didaftarkan
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "j F Y", // Format tanggal Indonesia: tanggal bulan tahun
                            static: true,
                            disableMobile: true,
                            allowInput: false,
                            // Pastikan field memiliki lebar yang sama dengan field lainnya
                            onReady: function(selectedDates, dateStr, instance) {
                                // Tetapkan gaya pada input yang terlihat (altInput) untuk memastikan ukuran yang sama
                                if (instance.altInput) {
                                    instance.altInput.style.width = "100%";
                                    instance.altInput.style.display = "block";

                                    // Pastikan bahwa container Flatpickr tidak mengubah lebar field
                                    const parentWrapper = instance.altInput.closest('.flatpickr-wrapper');
                                    if (parentWrapper) {
                                        parentWrapper.style.width = "100%";
                                        parentWrapper.style.display = "block";
                                    }

                                    // Pastikan inherit semua styling dari input asli
                                    instance.altInput.className = dateAcquiredInput.className;
                                }

                                // Override bulan dalam bahasa Inggris jika masih ada
                                const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                           "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                // Paksa penggunaan bulan Indonesia
                                if (instance.selectedDates && instance.selectedDates.length > 0) {
                                    const date = instance.selectedDates[0];
                                    const day = date.getDate();
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();

                                    // Set langsung ke input yang terlihat
                                    if (instance.altInput) {
                                        instance.altInput.value = `${day} ${month} ${year}`;
                                    }
                                }
                            },
                            onChange: function(selectedDates, dateStr, instance) {
                                if (selectedDates.length > 0) {
                                    const date = selectedDates[0];
                                    const day = date.getDate();
                                    // Gunakan nama bulan dalam bahasa Indonesia
                                    const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                               "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();

                                    // Set nilai langsung ke altInput untuk memastikan tampilan dalam bahasa Indonesia
                                    if (instance.altInput) {
                                        instance.altInput.value = `${day} ${month} ${year}`;
                                    }
                                }
                            },
                            // Fix timezone issue causing date to be off by one day
                            formatDate: (date, format) => {
                                // Force date parsing in local timezone
                                if (format === "Y-m-d") {
                                    const localDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                                    const year = localDate.getFullYear();
                                    const month = String(localDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(localDate.getDate()).padStart(2, '0');
                                    return `${year}-${month}-${day}`;
                                }

                                // Format khusus untuk tampilan dalam bahasa Indonesia
                                if (format === "j F Y") {
                                    const day = date.getDate();
                                    // Gunakan nama bulan dalam bahasa Indonesia
                                    const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                                "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();
                                    return `${day} ${month} ${year}`;
                                }

                                return flatpickr.formatDate(date, format);
                            },
                            // Ensure that when we parse dates, it's done in the local timezone
                            parseDate: (datestr, format) => {
                                if (format === "Y-m-d") {
                                    const [year, month, day] = datestr.split("-").map(Number);
                                    return new Date(year, month - 1, day);
                                }
                                return flatpickr.parseDate(datestr, format);
                            }
                        });
                    }
                },

                setupModalHelpers() {
                    window.openModal = window.openModal || function (modal, content) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                        }, 10);
                    };

                    window.closeModal = window.closeModal || function (modal, content) {
                        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        content.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    };
                },

                setupEventListeners() {
                    const updateBtn = document.getElementById('updateDepreciationBtn');
                    if (this.hasEditPermission && updateBtn) {
                        updateBtn.addEventListener('click', () => {
                            this.openUpdateModal();
                        });
                    }

                    document.querySelectorAll('.close-modal').forEach(button => {
                        button.addEventListener('click', () => {
                            const modalId = button.getAttribute('data-modal');
                            const modal = document.getElementById(modalId);
                            const content = document.getElementById(modalId + 'Content');
                            if (modal && content) {
                                closeModal(modal, content);
                            }
                        });
                    });

                    const updateSubmitBtn = document.getElementById('updateDepreciationSubmitBtn');
                    if (updateSubmitBtn) {
                        updateSubmitBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.submitUpdateForm();
                        });
                    }

                    const depreciationForm = document.getElementById('updateDepreciationForm');
                    if (depreciationForm) {
                        depreciationForm.querySelectorAll('select').forEach(select => {
                            select.addEventListener('change', () => {
                                select.classList.remove('border-red-500');
                                const errorElement = select.nextElementSibling;
                                if (errorElement && errorElement.classList.contains('error-message')) {
                                    errorElement.classList.add('hidden');
                                }
                            });
                        });

                        depreciationForm.querySelectorAll('input').forEach(input => {
                            input.addEventListener('input', () => {
                                input.classList.remove('border-red-500');
                                const errorElement = input.nextElementSibling;
                                if (errorElement && errorElement.classList.contains('error-message')) {
                                    errorElement.classList.add('hidden');
                                }
                            });
                        });
                    }

                    const percentageToggle = document.getElementById('percentageToggle');
                    if (percentageToggle) {
                        percentageToggle.addEventListener('change', () => {
                            this.isPercentageView = percentageToggle.checked;
                            if (this.originalChartData) {
                                this.updateChartDisplay();
                            }
                            if (this.currentDepreciation) {
                                this.updateDepreciationData(this.currentDepreciation);
                            }
                        });
                    }
                },

                setupFormInputs() {
                    const acquisitionCostInput = document.getElementById('acquisition_cost');
                    const salvageValueInput = document.getElementById('salvage_value');

                    // Format currency inputs
                    const formatCurrency = (element) => {
                        element.addEventListener('input', function (e) {
                            let value = this.value.replace(/[^\d]/g, '');

                            if (value) {
                                // Format with thousand separators
                                value = new Intl.NumberFormat('id-ID').format(value);
                            }

                            this.value = value;
                        });

                        // Format on focus out to ensure proper display
                        element.addEventListener('focusout', function (e) {
                            if (this.value === '') return;

                            let value = this.value.replace(/[^\d]/g, '');
                            if (value) {
                                value = new Intl.NumberFormat('id-ID').format(value);
                            }
                            this.value = value;
                        });

                        // On focus, position cursor at the end
                        element.addEventListener('focus', function (e) {
                            const val = this.value;
                            this.value = '';
                            this.value = val;
                        });
                    };

                    if (acquisitionCostInput) {
                        formatCurrency(acquisitionCostInput);
                    }

                    if (salvageValueInput) {
                        formatCurrency(salvageValueInput);
                    }
                },

                showFieldError(field, message) {
                    if (!field) return;

                    field.classList.add('border-red-500');
                    const errorElement = field.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        if (message) {
                            errorElement.textContent = message;
                        }
                        errorElement.classList.remove('hidden');
                    }
                },

                clearFieldErrors() {
                    const fields = document.querySelectorAll('#updateDepreciationForm input, #updateDepreciationForm select');
                    fields.forEach(field => {
                        field.classList.remove('border-red-500');
                        const errorElement = field.nextElementSibling;
                        if (errorElement && errorElement.classList.contains('error-message')) {
                            errorElement.classList.add('hidden');
                        }
                    });

                    const errorDiv = document.getElementById('update-form-error');
                    if (errorDiv) errorDiv.classList.add('hidden');
                },

                openUpdateModal() {
                    const modal = document.getElementById('updateDepreciationModal');
                    const content = document.getElementById('updateDepreciationModalContent');

                    if (!modal || !content) return;

                    this.clearFieldErrors();

                    if (this.currentDepreciation) {
                        let depMethod = this.currentDepreciation.depreciation_method || '';
                        if (depMethod === "Sum of the Year's Digits") {
                            depMethod = "Sum of the Years Digits";
                        }

                        document.getElementById('depreciation_method').value = depMethod;

                        const acquisitionCost = document.getElementById('acquisition_cost');
                        if (acquisitionCost && this.currentDepreciation.total_cost) {
                            acquisitionCost.value = parseInt(this.currentDepreciation.total_cost).toLocaleString('id-ID');
                        }

                        const salvageValue = document.getElementById('salvage_value');
                        if (salvageValue && this.currentDepreciation.salvage_value) {
                            salvageValue.value = parseInt(this.currentDepreciation.salvage_value).toLocaleString('id-ID');
                        }

                        const assetLife = document.getElementById('asset_life_months');
                        if (assetLife) {
                            assetLife.value = this.currentDepreciation.asset_life_months || '';
                        }

                        if (this.currentDepreciation.date_acquired && this.flatpickrInstance) {
                            // Memastikan tanggal yang ditampilkan sama dengan yang disimpan
                            console.log('Original date from server:', this.currentDepreciation.date_acquired);

                            // Parse tanggal dari string YYYY-MM-DD tanpa mempertimbangkan timezone
                            const [year, month, day] = this.currentDepreciation.date_acquired.split('-').map(Number);
                            const fixedDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                            console.log('Fixed date to set:', fixedDate);

                            // Atur tanggal tanpa waktu
                            this.flatpickrInstance.setDate(fixedDate, true, 'Y-m-d');
                            console.log('Date display value:', document.getElementById('date_acquired').value);

                            // Memastikan altInput memiliki lebar yang tepat
                            if (this.flatpickrInstance.altInput) {
                                setTimeout(() => {
                                    this.flatpickrInstance.altInput.style.width = "100%";
                                    this.flatpickrInstance.altInput.className = document.getElementById('date_acquired').className;

                                    const parentWrapper = this.flatpickrInstance.altInput.closest('.flatpickr-wrapper');
                                    if (parentWrapper) {
                                        parentWrapper.style.width = "100%";
                                    }
                                }, 0);
                            }

                            if (this.flatpickrInstance.selectedDates.length > 0) {
                                console.log('Selected date after setting:',
                                    this.flatpickrInstance.selectedDates[0].getFullYear() + '-' +
                                    (this.flatpickrInstance.selectedDates[0].getMonth() + 1) + '-' +
                                    this.flatpickrInstance.selectedDates[0].getDate()
                                );
                            }
                        }
                    }

                    openModal(modal, content);
                },

                submitUpdateForm() {
                    const form = document.getElementById('updateDepreciationForm');
                    const submitBtn = document.getElementById('updateDepreciationSubmitBtn');
                    const errorDiv = document.getElementById('update-form-error');

                    if (!form || !submitBtn) return;

                    if (errorDiv) {
                        errorDiv.textContent = '';
                        errorDiv.classList.add('hidden');
                    }

                    const depreciationMethodField = document.getElementById('depreciation_method');
                    const acquisitionCostField = document.getElementById('acquisition_cost');
                    const salvageValueField = document.getElementById('salvage_value');
                    const assetLifeMonthsField = document.getElementById('asset_life_months');
                    const dateAcquiredField = document.getElementById('date_acquired');

                    let depreciationMethod = depreciationMethodField.value;
                    let acquisitionCost = acquisitionCostField.value;
                    let salvageValue = salvageValueField.value;
                    const assetLifeMonths = assetLifeMonthsField.value;

                    // Dapatkan tanggal dalam format YYYY-MM-DD tanpa time component
                    let dateAcquired = '';
                    if (this.flatpickrInstance && this.flatpickrInstance.selectedDates.length > 0) {
                        const selectedDate = this.flatpickrInstance.selectedDates[0];
                        // Gunakan nilai tahun, bulan, dan hari langsung dari objek Date tanpa mempertimbangkan waktu
                        const year = selectedDate.getFullYear();
                        const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                        const day = String(selectedDate.getDate()).padStart(2, '0');
                        // Format string tanggal secara manual untuk memastikan tidak ada perubahan karena timezone
                        dateAcquired = `${year}-${month}-${day}`;
                        console.log('Date from flatpickr instance:', dateAcquired);
                    } else {
                        // Fallback jika tidak ada tanggal yang dipilih di flatpickr
                        dateAcquired = dateAcquiredField.value;
                        console.log('Date from input field:', dateAcquired);
                    }

                    const fields = [depreciationMethodField, acquisitionCostField, salvageValueField, assetLifeMonthsField, dateAcquiredField];
                    fields.forEach(field => {
                        field.classList.remove('border-red-500');
                        const errorElement = field.nextElementSibling;
                        if (errorElement && errorElement.classList.contains('error-message')) {
                            errorElement.classList.add('hidden');
                        }
                    });

                    let isValid = true;

                    if (!depreciationMethod) {
                        this.showFieldError(depreciationMethodField, "Metode penyusutan harus dipilih");
                        isValid = false;
                    }

                    if (!acquisitionCost) {
                        this.showFieldError(acquisitionCostField, "Biaya pengadaan harus diisi");
                        isValid = false;
                    }

                    if (!salvageValue) {
                        this.showFieldError(salvageValueField, "Nilai sisa harus diisi");
                        isValid = false;
                    }

                    if (!assetLifeMonths) {
                        this.showFieldError(assetLifeMonthsField, "Usia asset harus diisi");
                        isValid = false;
                    }

                    if (!dateAcquired) {
                        this.showFieldError(dateAcquiredField, "Tanggal pengadaan harus diisi");
                        isValid = false;
                    }

                    if (!isValid) {
                        this.showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
                        return;
                    }

                    if (depreciationMethod === "Sum of the Years Digits") {
                        depreciationMethod = "Sum of the Year's Digits";
                    }

                    acquisitionCost = acquisitionCost.replace(/[^\d]/g, '');
                    salvageValue = salvageValue.replace(/[^\d]/g, '');

                    if (parseInt(salvageValue) >= parseInt(acquisitionCost)) {
                        this.showToast('Nilai sisa harus lebih kecil dari biaya pengadaan', 'error');
                        return;
                    }

                    submitBtn.disabled = true;
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                    const formData = {
                        date_acquired: dateAcquired,
                        acquisition_cost: parseInt(acquisitionCost),
                        salvage_value: parseInt(salvageValue),
                        asset_life_months: parseInt(assetLifeMonths),
                        depreciation_method: depreciationMethod
                    };

                    console.log('Sending data to server:', formData);

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    $.ajax({
                        url: `/asset-depreciation/${this.assetId}`,
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify(formData),
                        success: (result) => {

                            if (result.success) {
                                this.showToast('Data penyusutan berhasil diperbarui', 'success');

                                const modal = document.getElementById('updateDepreciationModal');
                                const content = document.getElementById('updateDepreciationModalContent');
                                closeModal(modal, content);

                                this.loadDepreciationData();
                            } else {
                                let errorMessage = 'Gagal memperbarui data penyusutan';

                                if (result.errors) {
                                    if (typeof result.errors === 'string') {
                                        errorMessage = result.errors;
                                    } else if (typeof result.errors === 'object') {
                                        errorMessage = Object.values(result.errors).flat().join(', ');
                                    }
                                } else if (result.message) {
                                    errorMessage = result.message;
                                }

                                this.showToast(errorMessage, 'error');

                                if (errorDiv && result.errors) {
                                    errorDiv.textContent = errorMessage;
                                    errorDiv.classList.remove('hidden');
                                }
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error('Error updating depreciation:', status, error);

                            if (xhr.status === 401) {
                                this.showToast('Sesi anda telah berakhir. Silakan login kembali.', 'error');
                                setTimeout(() => {
                                    window.location.href = '/login';
                                }, 2000);
                                return;
                            }

                            let errorMessage = 'Terjadi kesalahan saat memperbarui data penyusutan';

                            try {
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.errors) {
                                        if (typeof xhr.responseJSON.errors === 'string') {
                                            errorMessage = xhr.responseJSON.errors;
                                        } else if (typeof xhr.responseJSON.errors === 'object') {
                                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                                        }
                                    } else if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                } else if (xhr.responseText) {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.errors) {
                                        errorMessage = typeof response.errors === 'object'
                                            ? Object.values(response.errors).flat().join(', ')
                                            : response.errors;
                                    } else if (response.message) {
                                        errorMessage = response.message;
                                    }
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }

                            this.showToast(errorMessage, 'error');

                            if (errorDiv) {
                                errorDiv.textContent = errorMessage;
                                errorDiv.classList.remove('hidden');
                            }
                        },
                        complete: () => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                },

                showLoading() {
                    document.getElementById('depreciationLoadingIndicator').classList.remove('hidden');
                    document.getElementById('contentSections').classList.add('hidden');
                    document.getElementById('depreciationErrorMessage').classList.add('hidden');
                },

                hideLoading() {
                    document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
                    document.getElementById('contentSections').classList.remove('hidden');
                },

                showError(message) {
                    const errorDiv = document.getElementById('depreciationErrorMessage');
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                    document.getElementById('contentSections').classList.add('hidden');
                    document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
                },

                showToast(message, type = 'success') {
                    let toastContainer = document.getElementById('depreciation-toast-container');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.id = 'depreciation-toast-container';
                        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                        document.body.appendChild(toastContainer);
                    }

                    const toast = document.createElement('div');

                    if (type === 'success') {
                        toast.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md flex items-center';
                    } else {
                        toast.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md flex items-center';
                    }

                    toast.innerHTML = `
                        <div class="py-1">
                            <svg class="h-6 w-6 mr-4 ${type === 'success' ? 'text-green-500' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">${type === 'success' ? 'Berhasil!' : 'Gagal!'}</p>
                            <p>${message}</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove()">×</button>
                    `;

                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    toast.style.transition = 'all 0.3s ease';
                    toastContainer.appendChild(toast);

                    setTimeout(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateX(0)';
                    }, 10);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 5000);
                },

                formatCurrency(value) {
                    if (value === null || value === undefined) return '-';
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                },

                formatPercentage(value, baseValue) {
                    if (value === null || value === undefined || baseValue === 0) return '-';
                    const percentage = (value / baseValue) * 100;
                    return percentage.toFixed(2) + '%';
                },

                loadDepreciationData() {
                    if (!this.assetId) {
                        this.showError('Asset ID tidak tersedia');
                        return;
                    }

                    this.showLoading();

                    fetch(`/asset-depreciation/${this.assetId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            return response.json().then(data => {
                                return {
                                    ok: response.ok,
                                    status: response.status,
                                    data: data
                                };
                            });
                        })
                        .then(result => {
                            const data = result.data;

                            if (!result.ok) {
                                console.warn(`HTTP status ${result.status} with response:`, data);

                                if (data &&
                                    (data.message?.includes('Aset tidak dapat didepresiasi') ||
                                        (typeof data.errors === 'string' && data.errors.includes('Aset tidak dapat didepresiasi')))) {

                                    this.showCannotDepreciateMessage(data.message || 'Aset tidak dapat didepresiasi');
                                    return;
                                }

                                throw new Error(data.errors || `Error HTTP: ${result.status}`);
                            }

                            if (data.success === false) {
                                if (data.errors &&
                                    (typeof data.errors === 'string' && data.errors.includes('Aset tidak dapat didepresiasi'))) {
                                    this.showCannotDepreciateMessage(data.errors);
                                    return;
                                }
                                throw new Error(data.errors || 'Gagal memuat data');
                            }

                            if (data.no_depreciation === true) {
                                this.showCannotDepreciateMessage(data.message || 'Aset tidak dapat didepresiasi');
                                return;
                            }

                            const depreciation = data.data?.depreciation || data?.depreciation;

                            if (!depreciation || Object.keys(depreciation).length === 0 || !depreciation.total_cost) {
                                this.showNoDepreciationData();
                                return;
                            }

                            this.currentDepreciation = depreciation;
                            this.updateDepreciationData(depreciation);
                            this.hideLoading();
                        })
                        .catch(error => {
                            console.error('Error loading depreciation data:', error);
                            this.showError('Gagal memuat data depresiasi: ' + error.message);
                        });
                },

                showNoDepreciationData() {
                    document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
                    document.getElementById('depreciationErrorMessage').classList.add('hidden');
                    document.getElementById('contentSections').classList.remove('hidden');

                    const noDataElement = document.getElementById('noDepreciationData');
                    if (noDataElement) {
                        noDataElement.classList.remove('hidden');
                        noDataElement.innerHTML = `
                                    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-8 rounded-lg mb-8 text-center">
                                        <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="text-lg font-medium mb-2">Asset Ini Belum Memiliki Data Penyusutan</h3>
                                        <p class="text-blue-600">Silakan gunakan tombol Pengaturan untuk menambahkan data penyusutan</p>
                                    </div>
                                `;
                    }

                    document.getElementById('depreciationChartContainer').classList.add('hidden');
                    document.getElementById('depreciationDetailsContainer').classList.add('hidden');
                },

                updateDepreciationData(depreciation) {
                    this.currentDepreciation = depreciation;

                    const totalCost = depreciation.total_cost || 0;
                    const salvageValue = depreciation.salvage_value || 0;
                    const currentValue = this.calculateCurrentValue(depreciation);

                    document.getElementById('depreciationSummary').innerHTML = `
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatIndonesianDate(depreciation.date_acquired)}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView ? '100%' : this.formatCurrency(totalCost)}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView ? this.formatPercentage(salvageValue, totalCost) : this.formatCurrency(salvageValue)}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.asset_life_months || '-'}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.getDepreciationMethodText(depreciation.depreciation_method)}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] font-semibold text-[#213268]">${this.isPercentageView ? this.formatPercentage(currentValue, totalCost) : this.formatCurrency(currentValue)}</td>
                            </tr>
                        `;

                    if (Array.isArray(depreciation.monthly_data)) {
                        const today = new Date();
                        const currentYear = today.getFullYear();
                        const currentMonth = today.getMonth() + 1;
                        const currentMonthMatch = this.findCurrentMonthMatch(depreciation.monthly_data, currentYear, currentMonth);
                        const mostRecentMonth = currentMonthMatch ||
                            this.findMostRecentMonth(depreciation.monthly_data, depreciation.date_acquired);
                        const highlightMonthNumber = mostRecentMonth ? mostRecentMonth.month_number : null;
                        const monthlyRows = depreciation.monthly_data.map(month => {
                            const expense = month.expense || 0;
                            const accumulatedDepreciation = month.accumulated_depreciation || 0;
                            const bookValue = month.book_value || 0;

                            const isCurrentValueRow = highlightMonthNumber &&
                                month.month_number &&
                                parseInt(month.month_number, 10) === parseInt(highlightMonthNumber, 10);

                            const highlightClass = isCurrentValueRow ?
                                'bg-blue-50 font-medium' : '';

                            const bookValueClass = isCurrentValueRow ?
                                'font-semibold text-[#213268]' : '';

                            return `
                            <tr class="${highlightClass}">
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_number}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_name}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView
                                    ? this.formatPercentage(expense, totalCost)
                                    : this.formatCurrency(expense)
                                }</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView
                                    ? this.formatPercentage(accumulatedDepreciation, totalCost)
                                    : this.formatCurrency(accumulatedDepreciation)
                                }</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] ${bookValueClass}">${this.isPercentageView
                                    ? this.formatPercentage(bookValue, totalCost)
                                    : this.formatCurrency(bookValue)
                                }${isCurrentValueRow ? ' <span class="text-xs text-blue-600">(Nilai Saat Ini)</span>' : ''}</td>
                            </tr>
                            `;
                        }).join('');
                        document.getElementById('monthlyDepreciationData').innerHTML = monthlyRows;
                    }

                    if (depreciation.chart_data) {
                        this.updateChartData(depreciation.chart_data);
                    }
                },

                getDepreciationMethodText(method) {
                    if (!method) return '-';

                    const methodMap = {
                        'Straight Line': 'Garis Lurus (Straight Line)',
                        'Declining Balance': 'Saldo Menurun (Declining Balance)',
                        'Double Declining Balance': 'Saldo Menurun Ganda (Double Declining Balance)',
                        '150% Declining Balance': 'Saldo Menurun 150% (150% Declining Balance)',
                        'Sum of the Year\'s Digits': 'Jumlah Digit Tahun (Sum of Year\'s Digits)'
                    };

                    return methodMap[method] || method;
                },

                updateChartData(chartData) {
                    this.originalChartData = { ...chartData };
                    this.updateChartDisplay();
                },

                updateChartDisplay() {
                    if (!this.originalChartData) return;

                    const ctx = document.getElementById('depreciationChart').getContext('2d');

                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const displayData = { ...this.originalChartData };
                    const assetLifeMonths = this.currentDepreciation?.asset_life_months || 0;
                    const showMonthlyView = assetLifeMonths <= 12;

                    let currentValueIndex = -1;

                    if (showMonthlyView && this.currentDepreciation && this.currentDepreciation.monthly_data) {
                        const monthlyData = this.currentDepreciation.monthly_data;
                        const totalCost = this.currentDepreciation.total_cost || 0;

                        const labels = [];
                        const values = [];

                        const today = new Date();
                        const currentYear = today.getFullYear();
                        const currentMonth = today.getMonth() + 1;
                        const currentMonthMatch = this.findCurrentMonthMatch(monthlyData, currentYear, currentMonth);
                        const mostRecentMonth = currentMonthMatch || this.findMostRecentMonth(monthlyData, this.currentDepreciation.date_acquired);

                        monthlyData.forEach((month, index) => {
                            if (month.month_name) {
                                const monthParts = month.month_name.split(' ');
                                if (monthParts.length > 0) {
                                    labels.push(monthParts[0]);
                                } else {
                                    labels.push(month.month_name);
                                }

                                const value = this.isPercentageView && totalCost > 0
                                    ? (month.book_value / totalCost) * 100
                                    : month.book_value;

                                values.push(value);

                                if (mostRecentMonth &&
                                    ((month.month_number && mostRecentMonth.month_number === month.month_number) ||
                                     (month.month_name && mostRecentMonth.month_name === month.month_name))) {
                                    currentValueIndex = index;
                                }
                            }
                        });

                        displayData.years = labels;
                        displayData.values = values;
                    } else if (displayData.values && displayData.values.length > 0) {
                        if (displayData.years && displayData.years.length > 0) {
                            const currentYear = new Date().getFullYear();
                            currentValueIndex = displayData.years.findIndex(year => parseInt(year) === currentYear);

                            if (currentValueIndex === -1) {
                                for (let i = displayData.years.length - 1; i >= 0; i--) {
                                    if (parseInt(displayData.years[i]) <= currentYear) {
                                        currentValueIndex = i;
                                        break;
                                    }
                                }
                            }
                        }

                        if (this.isPercentageView) {
                            const totalCost = this.currentDepreciation?.total_cost || 0;
                            displayData.values = displayData.values.map(value =>
                                totalCost > 0 ? (value / totalCost) * 100 : 0
                            );
                        }
                    }

                    const chartTitle = showMonthlyView ? 'Penyusutan Bulanan' : 'Penyusutan Tahunan';
                    document.querySelector('#depreciationChartContainer h3').textContent = chartTitle;

                    const pointRadius = Array(displayData.values.length).fill(4);
                    const pointBackgroundColors = Array(displayData.values.length).fill('#36A2EB');
                    const borderWidth = Array(displayData.values.length).fill(2);

                    if (currentValueIndex >= 0) {
                        pointRadius[currentValueIndex] = 8;
                        pointBackgroundColors[currentValueIndex] = '#FF6384';
                        borderWidth[currentValueIndex] = 3;
                    }

                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: displayData.years,
                            datasets: [{
                                data: displayData.values,
                                borderColor: '#36A2EB',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                pointBackgroundColor: pointBackgroundColors,
                                pointRadius: pointRadius,
                                borderWidth: 2,
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            let label = '';

                                            if (context.dataIndex === currentValueIndex) {
                                                label = 'Nilai Saat Ini: ';
                                            }

                                            if (this.isPercentageView) {
                                                label += context.parsed.y.toFixed(2) + '%';
                                            } else {
                                                label += this.formatCurrency(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => {
                                            if (this.isPercentageView) {
                                                return value + '%';
                                            } else {
                                                if (value === 0) return '0';
                                                return (value / 1000000).toFixed(1) + 'M';
                                            }
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    }
                                },
                                x: {
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                },

                updateChart(chartData) {
                    this.updateChartData(chartData);
                },

                showCannotDepreciateMessage(message) {
                    document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
                    document.getElementById('depreciationErrorMessage').classList.add('hidden');
                    document.getElementById('contentSections').classList.remove('hidden');

                    const noDataElement = document.getElementById('noDepreciationData');
                    if (noDataElement) {
                        noDataElement.classList.remove('hidden');
                        noDataElement.innerHTML = `
                                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-8 rounded-lg mb-8 text-center">
                                    <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">Aset Ini Tidak Dapat Didepresiasi</h3>
                                    <p class="text-blue-600">${message || 'Silakan periksa jenis aset atau nilai pengadaan'}</p>
                                </div>
                            `;
                    }

                    document.getElementById('depreciationChartContainer').classList.add('hidden');
                    document.getElementById('depreciationDetailsContainer').classList.add('hidden');
                },

                calculateCurrentValue(depreciation) {
                    if (!depreciation || !depreciation.monthly_data || !Array.isArray(depreciation.monthly_data) || depreciation.monthly_data.length === 0) {
                        return depreciation.total_cost || 0;
                    }

                    const today = new Date();
                    const currentYear = today.getFullYear();
                    const currentMonth = today.getMonth() + 1;

                    let currentValue = depreciation.total_cost || 0;

                    const currentMonthMatch = this.findCurrentMonthMatch(depreciation.monthly_data, currentYear, currentMonth);

                    if (currentMonthMatch !== null) {
                        currentValue = currentMonthMatch.book_value || currentValue;
                    } else {
                        const mostRecentMonth = this.findMostRecentMonth(depreciation.monthly_data, depreciation.date_acquired);
                        if (mostRecentMonth !== null) {
                            currentValue = mostRecentMonth.book_value || currentValue;
                        }
                    }

                    return currentValue;
                },

                findCurrentMonthMatch(monthlyData, currentYear, currentMonth) {
                    const monthNames = {
                        1: ['january', 'januari', 'jan'],
                        2: ['february', 'februari', 'feb'],
                        3: ['march', 'maret', 'mar'],
                        4: ['april', 'apr'],
                        5: ['may', 'mei'],
                        6: ['june', 'juni', 'jun'],
                        7: ['july', 'juli', 'jul'],
                        8: ['august', 'agustus', 'aug', 'agu', 'agt'],
                        9: ['september', 'sep', 'sept'],
                        10: ['october', 'oktober', 'oct', 'okt'],
                        11: ['november', 'nov'],
                        12: ['december', 'desember', 'dec', 'des']
                    };

                    for (const month of monthlyData) {
                        if (month.month_name) {
                            const parts = month.month_name.split(/\s+/);
                            if (parts.length >= 2) {
                                const monthName = parts[0].toLowerCase();
                                const year = parseInt(parts[parts.length - 1], 10);

                                if (year === currentYear) {
                                    for (const [num, names] of Object.entries(monthNames)) {
                                        if (parseInt(num, 10) === currentMonth) {
                                            if (names.includes(monthName)) {
                                                return month;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    return null;
                },

                findMostRecentMonth(monthlyData, dateAcquired) {
                    const today = new Date();
                    let mostRecentMonth = null;
                    let mostRecentDate = null;

                    const parseMonth = (monthName) => {
                        const monthMap = {
                            'january': 1, 'januari': 1, 'jan': 1,
                            'february': 2, 'februari': 2, 'feb': 2,
                            'march': 3, 'maret': 3, 'mar': 3,
                            'april': 4, 'apr': 4,
                            'may': 5, 'mei': 5,
                            'june': 6, 'juni': 6, 'jun': 6,
                            'july': 7, 'juli': 7, 'jul': 7,
                            'august': 8, 'agustus': 8, 'aug': 8, 'agt': 8, 'agu': 8,
                            'september': 9, 'sep': 9, 'sept': 9,
                            'october': 10, 'oktober': 10, 'oct': 10, 'okt': 10,
                            'november': 11, 'nov': 11,
                            'december': 12, 'desember': 12, 'dec': 12, 'des': 12
                        };

                        const parts = monthName.toLowerCase().split(/\s+/);
                        if (parts.length >= 2) {
                            const monthPart = parts[0];
                            const yearPart = parseInt(parts[parts.length - 1], 10);

                            if (monthMap[monthPart] && !isNaN(yearPart)) {
                                return { month: monthMap[monthPart], year: yearPart };
                            }
                        }
                        return null;
                    };

                    for (const month of monthlyData) {
                        let monthDate = null;

                        if (month.month_name) {
                            const parsed = parseMonth(month.month_name);
                            if (parsed) {
                                try {
                                    monthDate = new Date(parsed.year, parsed.month - 1, 28);
                                } catch (e) {
                                    continue;
                                }
                            }
                        }
                        else if (month.month_number && dateAcquired) {
                            try {
                                const startDate = new Date(dateAcquired);
                                monthDate = new Date(startDate);
                                monthDate.setMonth(startDate.getMonth() + (parseInt(month.month_number, 10) - 1));
                            } catch (e) {
                                continue;
                            }
                        }

                        if (monthDate && monthDate <= today) {
                            if (mostRecentDate === null || monthDate > mostRecentDate) {
                                mostRecentDate = monthDate;
                                mostRecentMonth = month;
                            }
                        }
                    }

                    return mostRecentMonth;
                },

                formatIndonesianDate(dateString) {
                    if (!dateString) return '-';

                    try {
                        const [year, month, day] = dateString.split('-').map(Number);
                        if (!year || !month || !day) {
                    const date = new Date(dateString);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    return date.toLocaleDateString('id-ID', options);
                        }

                        const date = new Date(year, month - 1, day);
                        const options = { day: 'numeric', month: 'long', year: 'numeric' };
                        return date.toLocaleDateString('id-ID', options);
                    } catch (e) {
                        console.error('Error formatting date:', e);
                        return dateString;
                    }
                }
            };

            DepreciationSystem.init();

            window.DepreciationSystem = DepreciationSystem;

            if (!{{ hasPermission('depreciation:edit') ? 'true' : 'false' }}) {
                const updateButtons = document.querySelectorAll('#updateDepreciationBtn');
                updateButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endpush
