<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">PENYUSUTAN</h2>
        <div class="flex items-center gap-4">
            <div class="flex items-center">
                <span class="text-sm text-gray-600 mr-2">Nilai</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="percentageToggle" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#213268] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]"></div>
                    <span class="ml-2 text-sm text-gray-600">Persen</span>
                </label>
            </div>
            @if(hasPermission('asset:depreciation:edit'))
            <button id="updateDepreciationBtn" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
    <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
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
                </tr>
            </thead>
                <tbody id="depreciationSummary">
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

    <!-- No Depreciation Data Message -->
    <div id="noDepreciationData" class="hidden">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-8 rounded-lg mb-8 text-center">
            <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium mb-2">Asset Ini Belum Memiliki Data Penyusutan</h3>
        </div>
    </div>

    <!-- Depreciation Chart -->
    <div id="depreciationChartContainer" class="bg-white p-6 rounded-lg shadow-sm mb-8">
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
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Penyusutan Akumulasi di Akhir Bulan</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nilai Buku di Akhir Bulan</th>
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
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="updateDepreciationModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="updateDepreciationForm" action="{{ route('asset-depreciation.update', ['assetId' => $asset['asset_id'] ?? '']) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-6">
                                <!-- Depreciation Method -->
                                <div>
                                    <label for="depreciation_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Penyusutan <span class="text-red-500">*</span></label>
                                    <select id="depreciation_method" name="depreciation_method"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" required>
                                        <option value="">Pilih Metode Penyusutan</option>
                                        <option value="Straight Line">Garis Lurus (Straight Line)</option>
                                        <option value="Declining Balance">Saldo Menurun (Declining Balance)</option>
                                        <option value="Double Declining Balance">Saldo Menurun Ganda (Double Declining Balance)</option>
                                        <option value="150% Declining Balance">Saldo Menurun 150% (150% Declining Balance)</option>
                                        <option value="Sum of the Years Digits">Jumlah Digit Tahun (Sum of Year's Digits)</option>
                                    </select>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus dipilih</div>
                                </div>

                                <!-- Acquisition Cost and Salvage Value side by side -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="acquisition_cost" class="block text-sm font-medium text-gray-700 mb-1">Biaya Pengadaan <span class="text-red-500">*</span></label>
                                        <input type="text" id="acquisition_cost" name="acquisition_cost" placeholder="0"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pengadaan harus diisi</div>
                                    </div>
                                    <div>
                                        <label for="salvage_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai Sisa <span class="text-red-500">*</span></label>
                                        <input type="text" id="salvage_value" name="salvage_value" placeholder="0"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus diisi</div>
                                    </div>
                                </div>

                                <!-- Asset Life and Date Acquired side by side -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="asset_life_months" class="block text-sm font-medium text-gray-700 mb-1">Usia Asset (bulan) <span class="text-red-500">*</span></label>
                                        <input type="number" id="asset_life_months" name="asset_life_months" min="1" max="360"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Usia asset harus diisi</div>
                                    </div>
                                    <div>
                                        <label for="date_acquired" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengadaan <span class="text-red-500">*</span></label>
                                        <input type="date" id="date_acquired" name="date_acquired"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pengadaan harus diisi</div>
                                    </div>
                                </div>

                                <!-- Error message container -->
                                <div id="update-form-error" class="hidden text-red-500 text-sm p-2 bg-red-50 rounded-md mt-2 mb-4"></div>

                                <!-- Form Actions -->
                                <div class="flex justify-end">
                                    <button type="button" id="updateDepreciationSubmitBtn" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#162249]">
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
    document.addEventListener('DOMContentLoaded', function() {
    const DepreciationSystem = {
        initialized: false,
        assetId: {{ $asset['asset_id'] ?? 'null' }},
        apiBaseUrl: "{{ config('app.api_url', '') }}",
        chart: null,
        currentDepreciation: null,
        isPercentageView: true,
        originalChartData: null,
        hasEditPermission: {{ hasPermission('asset:depreciation:edit') ? 'true' : 'false' }},

        init() {
            if (this.initialized) return;
            console.log('Memulai Sistem Penyusutan dengan Asset ID:', this.assetId);

            if (!this.assetId) {
                console.error('Asset ID tidak tersedia');
                this.showError('Asset ID tidak tersedia');
                return;
            }

            this.setupModalHelpers();
            this.setupEventListeners();
            this.setupFormInputs();
            this.loadDepreciationData();
            this.initialized = true;
        },

        setupModalHelpers() {
            // Define openModal and closeModal functions if not already defined
            window.openModal = window.openModal || function(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            };

            window.closeModal = window.closeModal || function(modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };
        },

        setupEventListeners() {
            // Update Depreciation Button
            const updateBtn = document.getElementById('updateDepreciationBtn');
            if (this.hasEditPermission && updateBtn) {
                updateBtn.addEventListener('click', () => {
                    this.openUpdateModal();
                });
            }

            // Close Modal Buttons
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

            // Add direct click handler for update button instead of form submit
            const updateSubmitBtn = document.getElementById('updateDepreciationSubmitBtn');
            if (updateSubmitBtn) {
                updateSubmitBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.submitUpdateForm();
                });
            }

            // Add input event listeners to clear field validation errors when typing
            const depreciationForm = document.getElementById('updateDepreciationForm');
            if (depreciationForm) {
                // For select fields
                depreciationForm.querySelectorAll('select').forEach(select => {
                    select.addEventListener('change', () => {
                        select.classList.remove('border-red-500');
                        const errorElement = select.nextElementSibling;
                        if (errorElement && errorElement.classList.contains('error-message')) {
                            errorElement.classList.add('hidden');
                        }
                    });
                });

                // For input fields
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

            // Percentage toggle
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
            // Format currency inputs
            const acquisitionCostInput = document.getElementById('acquisition_cost');
            const salvageValueInput = document.getElementById('salvage_value');

            if (acquisitionCostInput) {
                acquisitionCostInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^\d]/g, '');
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID');
                    }
                    this.value = value;
                });
            }

            if (salvageValueInput) {
                salvageValueInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^\d]/g, '');
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID');
                    }
                    this.value = value;
                });
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

            // Clear previous error messages and field error styling
            this.clearFieldErrors();

            // Populate form with current values if available
            if (this.currentDepreciation) {
                // Handle special case for Sum of the Year's Digits
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

                const dateAcquired = document.getElementById('date_acquired');
                if (dateAcquired && this.currentDepreciation.date_acquired) {
                    dateAcquired.value = this.currentDepreciation.date_acquired;
                }
            }

            // Open modal
            openModal(modal, content);
        },

        submitUpdateForm() {
            const form = document.getElementById('updateDepreciationForm');
            const submitBtn = document.getElementById('updateDepreciationSubmitBtn');
            const errorDiv = document.getElementById('update-form-error');

            if (!form || !submitBtn) return;

            // Clear previous error messages
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.classList.add('hidden');
            }

            // Get form fields
            const depreciationMethodField = document.getElementById('depreciation_method');
            const acquisitionCostField = document.getElementById('acquisition_cost');
            const salvageValueField = document.getElementById('salvage_value');
            const assetLifeMonthsField = document.getElementById('asset_life_months');
            const dateAcquiredField = document.getElementById('date_acquired');

            // Get form values
            let depreciationMethod = depreciationMethodField.value;
            let acquisitionCost = acquisitionCostField.value;
            let salvageValue = salvageValueField.value;
            const assetLifeMonths = assetLifeMonthsField.value;
            const dateAcquired = dateAcquiredField.value;

            // Reset all error states
            const fields = [depreciationMethodField, acquisitionCostField, salvageValueField, assetLifeMonthsField, dateAcquiredField];
            fields.forEach(field => {
                field.classList.remove('border-red-500');
                const errorElement = field.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.classList.add('hidden');
                }
            });

            // Validate each field individually and show specific error messages
            let isValid = true;

            // Validate depreciation method
            if (!depreciationMethod) {
                this.showFieldError(depreciationMethodField, "Metode penyusutan harus dipilih");
                isValid = false;
            }

            // Validate acquisition cost
            if (!acquisitionCost) {
                this.showFieldError(acquisitionCostField, "Biaya pengadaan harus diisi");
                isValid = false;
            }

            // Validate salvage value
            if (!salvageValue) {
                this.showFieldError(salvageValueField, "Nilai sisa harus diisi");
                isValid = false;
            }

            // Validate asset life months
            if (!assetLifeMonths) {
                this.showFieldError(assetLifeMonthsField, "Usia asset harus diisi");
                isValid = false;
            }

            // Validate date acquired
            if (!dateAcquired) {
                this.showFieldError(dateAcquiredField, "Tanggal pengadaan harus diisi");
                isValid = false;
            }

            // Don't proceed if validation fails
            if (!isValid) {
                this.showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
                return;
            }

            // Handle special case for Sum of the Years Digits
            if (depreciationMethod === "Sum of the Years Digits") {
                depreciationMethod = "Sum of the Year's Digits";
            }

            // Convert formatted currency to numbers
            acquisitionCost = acquisitionCost.replace(/[^\d]/g, '');
            salvageValue = salvageValue.replace(/[^\d]/g, '');

            // Validate numeric values
            if (parseInt(salvageValue) >= parseInt(acquisitionCost)) {
                this.showToast('Nilai sisa harus lebih kecil dari biaya pengadaan', 'error');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            // Prepare data for submission
            const formData = {
                date_acquired: dateAcquired,
                acquisition_cost: parseInt(acquisitionCost),
                salvage_value: parseInt(salvageValue),
                asset_life_months: parseInt(assetLifeMonths),
                depreciation_method: depreciationMethod
            };

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            console.log('Sending depreciation update for Asset ID:', this.assetId, formData);

            // Submit using jQuery AJAX for compatibility with other parts of the system
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
                    console.log('Depreciation update result:', result);

                    if (result.success) {
                        // Success case
                        this.showToast('Data penyusutan berhasil diperbarui', 'success');

                        // Close modal
                        const modal = document.getElementById('updateDepreciationModal');
                        const content = document.getElementById('updateDepreciationModalContent');
                        closeModal(modal, content);

                        // Reload depreciation data
                        this.loadDepreciationData();
                    } else {
                        // Error with response
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

                        // Display detailed errors in the error div if available
                        if (errorDiv && result.errors) {
                            errorDiv.textContent = errorMessage;
                            errorDiv.classList.remove('hidden');
                        }
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error updating depreciation:', status, error);
                    console.log('Response:', xhr.responseText);

                    if (xhr.status === 401) {
                        // Auth error - redirect to login
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

                    // Display detailed errors in the error div
                    if (errorDiv) {
                        errorDiv.textContent = errorMessage;
                        errorDiv.classList.remove('hidden');
                    }
                },
                complete: () => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        },

        showLoading() {
            document.getElementById('depreciationLoadingIndicator').classList.remove('hidden');
            document.getElementById('contentSections').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        },

        hideLoading() {
            document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
            document.getElementById('contentSections').classList.remove('hidden');
        },

        showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            document.getElementById('contentSections').classList.add('hidden');
            document.getElementById('depreciationLoadingIndicator').classList.add('hidden');
        },

        showToast(message, type = 'success') {
            // Get toast container
            let toastContainer = document.getElementById('depreciation-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'depreciation-toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Create the toast element
            const toast = document.createElement('div');

            // Set classes based on type
            if (type === 'success') {
                toast.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md flex items-center';
            } else {
                toast.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md flex items-center';
            }

            // Add content
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

            // Add to container with smooth animation
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            toastContainer.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Auto-remove after 5 seconds
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
            console.log('Mengambil data penyusutan untuk Asset ID:', this.assetId);

            fetch(`/asset-depreciation/${this.assetId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Menerima data penyusutan:', data);

                if (data.success === false) {
                    throw new Error( data.errors || 'Gagal memuat data');
                }

                // Check if depreciation data exists
                const depreciation = data.data?.depreciation || data?.depreciation;

                if (!depreciation || Object.keys(depreciation).length === 0 || !depreciation.total_cost) {
                    // Asset has no depreciation data
                    this.showNoDepreciationData();
                    return;
                }

                // Store current depreciation data for form population
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
            // Hide loading indicator
            document.getElementById('loadingIndicator').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');

            // Show content sections but only the no data message
            document.getElementById('contentSections').classList.remove('hidden');

            // Show no data message
            document.getElementById('noDepreciationData').classList.remove('hidden');

            // Hide other content
            document.getElementById('depreciationChartContainer').classList.add('hidden');
            document.getElementById('depreciationDetailsContainer').classList.add('hidden');
        },

        updateDepreciationData(depreciation) {
            // Store current depreciation data for reference
            this.currentDepreciation = depreciation;

            const totalCost = depreciation.total_cost || 0;
            const salvageValue = depreciation.salvage_value || 0;

            // Update summary table
            document.getElementById('depreciationSummary').innerHTML = `
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.date_acquired || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView ? '100%' : this.formatCurrency(totalCost)}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.isPercentageView ? this.formatPercentage(salvageValue, totalCost) : this.formatCurrency(salvageValue)}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.asset_life_months || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.getDepreciationMethodText(depreciation.depreciation_method)}</td>
                </tr>
            `;

            // Update monthly data table
            if (Array.isArray(depreciation.monthly_data)) {
                const monthlyRows = depreciation.monthly_data.map(month => {
                    const expense = month.expense || 0;
                    const accumulatedDepreciation = month.accumulated_depreciation || 0;
                    const bookValue = month.book_value || 0;

                    return `
                    <tr>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_number}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_name}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${
                            this.isPercentageView
                                ? this.formatPercentage(expense, totalCost)
                                : this.formatCurrency(expense)
                        }</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${
                            this.isPercentageView
                                ? this.formatPercentage(accumulatedDepreciation, totalCost)
                                : this.formatCurrency(accumulatedDepreciation)
                        }</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${
                            this.isPercentageView
                                ? this.formatPercentage(bookValue, totalCost)
                                : this.formatCurrency(bookValue)
                        }</td>
                    </tr>
                    `;
                }).join('');
                document.getElementById('monthlyDepreciationData').innerHTML = monthlyRows;
            }

            // Update chart
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
            this.originalChartData = {...chartData};
            this.updateChartDisplay();
        },

        updateChartDisplay() {
            if (!this.originalChartData) return;

            const ctx = document.getElementById('depreciationChart').getContext('2d');

            // Destroy existing chart if it exists
            if (this.chart) {
                this.chart.destroy();
            }

            // Create a copy of chart data to manipulate
            const displayData = {...this.originalChartData};

            // If percentage view is enabled, convert values to percentages
            if (this.isPercentageView && displayData.values && displayData.values.length > 0) {
                const initialValue = displayData.values[0];
                displayData.values = displayData.values.map(value =>
                    initialValue > 0 ? (value / initialValue) * 100 : 0
                );
            }

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayData.years,
                    datasets: [{
                        data: displayData.values,
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        pointBackgroundColor: '#36A2EB',
                        pointRadius: 4,
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
                                    if (this.isPercentageView) {
                                        return context.parsed.y.toFixed(2) + '%';
                                    } else {
                                        return this.formatCurrency(context.parsed.y);
                                    }
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
        }
    };

    // Initialize the Depreciation System
    DepreciationSystem.init();

    // Make DepreciationSystem available globally (for debugging)
    window.DepreciationSystem = DepreciationSystem;

    // Add JavaScript to hide elements based on permissions
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
