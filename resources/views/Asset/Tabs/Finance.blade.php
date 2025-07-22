<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-[#213268]">TRANSAKSI KEUANGAN</h2>
        @if(hasPermission('asset:transaction:create'))
            <button id="addTransactionBtn" type="button"
                class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                TAMBAH TRANSAKSI
            </button>
        @endif
    </div>

    <!-- Loading indicator -->
    <div id="financeLoadingIndicator" class="flex justify-center items-center py-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#213268]"></div>
        <span class="ml-2 text-gray-600">Memuat data transaksi...</span>
    </div>

    <!-- Error message container -->
    <div id="financeErrorMessage"
        class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    </div>

    <!-- Content sections -->
    <div id="financeContentSections" class="hidden">
        <!-- Transaction Filter/Sort -->
        <div
            class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4 bg-gray-50 p-3 rounded-md gap-4">
            <div class="text-sm font-medium text-gray-700" id="transaction-count">Total Transaksi: 0</div>
            <div class="flex space-x-2">
                <select id="filter-type"
                    class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                    <option value="all">Semua Tipe</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
                <select id="sort-by"
                    class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="amount-high">Nominal (Tinggi-Rendah)</option>
                    <option value="amount-low">Nominal (Rendah-Tinggi)</option>
                </select>
            </div>
        </div>

        <!-- Transactions List with better headers -->
        @if(hasPermission('asset:transaction:view'))
            <div class="overflow-x-auto mb-8">
                <table class="w-full min-w-[500px] border-collapse">
                    <thead>
                        <tr>
                            <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-3/12">Tanggal</th>
                            <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-2/12">Tipe</th>
                            <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-right w-3/12">Nominal</th>
                            <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-3/12">Keterangan
                            </th>
                            <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-center w-1/12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-items">
                        <!-- Transaction items will be loaded here -->
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Summary in cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-4 bg-red-50 border border-red-100 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="mr-2 p-2 bg-red-500 text-white rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700">Total Pengeluaran</h3>
                </div>
                <p id="expense-total" class="text-xl font-bold text-red-600">Rp 0,00</p>
            </div>

            <div class="p-4 bg-green-50 border border-green-100 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="mr-2 p-2 bg-green-500 text-white rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700">Total Pemasukan</h3>
                </div>
                <p id="income-total" class="text-xl font-bold text-green-600">Rp 0,00</p>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex items-center mb-2">
                    <div class="mr-2 p-2 bg-blue-500 text-white rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700">Saldo Total</h3>
                </div>
                <p id="balance-total" class="text-xl font-bold text-blue-600">Rp 0,00</p>
            </div>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    @if(hasPermission('asset:transaction:create'))
        <div id="addTransactionModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addTransactionModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH TRANSAKSI BARU</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addTransactionModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="addTransactionForm" action="{{ route('asset-transactions.store') }}" method="POST"
                            data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset_id field -->
                                    <input type="hidden" id="asset-id" name="asset_id"
                                        value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Type Input with better options -->
                                    <div class="space-y-2">
                                        <label for="transaction-type"
                                            class="block text-base font-semibold text-[#666666]">Tipe <span
                                                class="text-red-500">*</span></label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label
                                                class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="type" value="expense"
                                                    class="mr-2 text-[#213268] focus:ring-[#213268]">
                                                <div class="flex items-center">
                                                    <div class="p-1.5 bg-red-100 rounded-md mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                        </svg>
                                                    </div>
                                                    <span>Pengeluaran</span>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="type" value="income"
                                                    class="mr-2 text-[#213268] focus:ring-[#213268]">
                                                <div class="flex items-center">
                                                    <div class="p-1.5 bg-green-100 rounded-md mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                        </svg>
                                                    </div>
                                                    <span>Pemasukan</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Date Input -->
                                    <div class="space-y-2">
                                        <label for="transaction-date"
                                            class="block text-base font-semibold text-[#666666]">Tanggal <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" id="transaction-date" name="transaction_date" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    </div>

                                    <!-- Amount Input with currency prefix -->
                                    <div class="space-y-2">
                                        <label for="transaction-amount"
                                            class="block text-base font-semibold text-[#666666]">Nominal <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" id="transaction-amount" name="amount" required
                                                placeholder="0,00"
                                                class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        </div>
                                    </div>

                                    <!-- Notes Input -->
                                    <div class="space-y-2">
                                        <label for="transaction-description"
                                            class="block text-base font-semibold text-[#666666]">Keterangan</label>
                                        <textarea id="transaction-description" name="description" rows="3"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Keterangan transaksi"></textarea>
                                    </div>

                                    <!-- Error message -->
                                    <div id="form-error" class="text-red-500 text-sm hidden"></div>

                                    <!-- Save Button -->
                                    <div class="pt-4 flex gap-4">
                                        <button type="button" id="addTransactionSubmitBtn"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Transaction Modal -->
    @if(hasPermission('asset:transaction:edit'))
        <div id="editTransactionModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editTransactionModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT TRANSAKSI</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editTransactionModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="editTransactionForm"
                            action="{{ route('asset-transactions.update', ['transactionId' => '_id_']) }}" method="POST"
                            data-no-loading>
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden fields -->
                                    <input type="hidden" id="edit-transaction-id" name="transaction_id">
                                    <input type="hidden" id="edit-asset-id" name="asset_id"
                                        value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Type Input -->
                                    <div class="space-y-2">
                                        <label for="edit-transaction-type"
                                            class="block text-base font-semibold text-[#666666]">Tipe <span
                                                class="text-red-500">*</span></label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label
                                                class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="type" value="expense" id="edit-type-expense"
                                                    class="mr-2 text-[#213268] focus:ring-[#213268]">
                                                <div class="flex items-center">
                                                    <div class="p-1.5 bg-red-100 rounded-md mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                        </svg>
                                                    </div>
                                                    <span>Pengeluaran</span>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="type" value="income" id="edit-type-income"
                                                    class="mr-2 text-[#213268] focus:ring-[#213268]">
                                                <div class="flex items-center">
                                                    <div class="p-1.5 bg-green-100 rounded-md mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                        </svg>
                                                    </div>
                                                    <span>Pemasukan</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Date Input -->
                                    <div class="space-y-2">
                                        <label for="edit-transaction-date"
                                            class="block text-base font-semibold text-[#666666]">Tanggal <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" id="edit-transaction-date" name="transaction_date" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    </div>

                                    <!-- Amount Input -->
                                    <div class="space-y-2">
                                        <label for="edit-transaction-amount"
                                            class="block text-base font-semibold text-[#666666]">Nominal <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" id="edit-transaction-amount" name="amount" required
                                                placeholder="0,00"
                                                class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        </div>
                                    </div>

                                    <!-- Notes Input -->
                                    <div class="space-y-2">
                                        <label for="edit-transaction-description"
                                            class="block text-base font-semibold text-[#666666]">Keterangan</label>
                                        <textarea id="edit-transaction-description" name="description" rows="3"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Keterangan transaksi"></textarea>
                                    </div>

                                    <!-- Error message -->
                                    <div id="edit-form-error" class="text-red-500 text-sm hidden"></div>

                                    <!-- Save Button -->
                                    <div class="pt-4 flex gap-4">
                                        <button type="submit" id="editTransactionSubmitBtn"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Transaction Modal -->
    @if(hasPermission('asset:transaction:delete'))
        <div id="deleteTransactionModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteTransactionModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS TRANSAKSI</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteTransactionModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteTransactionForm"
                            action="{{ route('asset-transactions.destroy', ['transactionId' => '_id_']) }}" method="POST"
                            data-no-loading>
                            @csrf
                            @method('DELETE')
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus
                                            transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteTransactionModal">
                                            Batal
                                        </button>
                                        <button type="submit" id="deleteTransactionSubmitBtn"
                                            class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Toast Notification Container -->
<div id="finance-toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function showLoading() {
            document.getElementById('financeLoadingIndicator').classList.remove('hidden');
            document.getElementById('financeContentSections').classList.add('hidden');
            document.getElementById('financeErrorMessage').classList.add('hidden');
        }

        function hideLoading() {
            document.getElementById('financeLoadingIndicator').classList.add('hidden');
            document.getElementById('financeContentSections').classList.remove('hidden');
        }

        function showError(message) {
            const errorDiv = document.getElementById('financeErrorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            document.getElementById('financeLoadingIndicator').classList.add('hidden');
            document.getElementById('financeContentSections').classList.add('hidden');
        }

        const initializeToastContainer = () => {
            let toastContainer = document.getElementById('finance-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'finance-toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }
        };

        initializeToastContainer();

        const tabTriggers = document.querySelectorAll('[data-tab]');
        if (tabTriggers.length > 0) {
            tabTriggers.forEach(trigger => {
                trigger.addEventListener('click', function (e) {
                    const tabId = this.getAttribute('data-tab');
                    if (tabId === 'finance') {
                        setTimeout(initializeToastContainer, 100);
                    }
                });
            });
        }

        function showToast(message, type = 'success') {
            initializeToastContainer();

            let toastContainer = document.getElementById('finance-toast-container');
            if (!toastContainer) {
                console.error('Toast container still not found!');
                toastContainer = document.createElement('div');
                toastContainer.id = 'finance-toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2';
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

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 5000);
        }

        window.financeShowToast = showToast;
        window.showToast = showToast;

        window.openTransactionModal = function () {
            const modal = document.getElementById('addTransactionModal');
            const modalContent = document.getElementById('addTransactionModalContent');
            if (modal && modalContent) {
                modal.classList.remove('hidden');
                setTimeout(function () {
                    modalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    modalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }
        };

        function setupModalHelpers() {
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
        }

        setupModalHelpers();

        function setupEventListeners() {
            const addBtn = document.getElementById('addTransactionBtn');
            if (addBtn) {
                addBtn.onclick = null;
                addBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modal = document.getElementById('addTransactionModal');
                    const content = document.getElementById('addTransactionModalContent');
                    if (modal && content) {
                        document.getElementById('addTransactionForm').reset();
                        openModal(modal, content);
                    }
                });
            }

            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal') || button.closest('[id$="Modal"]').id;
                    const modal = document.getElementById(modalId);
                    const content = document.getElementById(modalId + 'Content');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.edit-transaction')) {
                    @if(hasPermission('asset:transaction:edit'))
                        e.preventDefault();
                        const editBtn = e.target.closest('.edit-transaction');
                        const transactionId = editBtn.getAttribute('data-id');
                        editTransaction(transactionId);
                    @else
                        e.preventDefault();
                        financeShowToast('Anda tidak memiliki izin untuk mengedit transaksi', 'error');
                    @endif
                }

                if (e.target.closest('.delete-transaction')) {
                    @if(hasPermission('asset:transaction:delete'))
                        e.preventDefault();
                        const deleteBtn = e.target.closest('.delete-transaction');
                        const transactionId = deleteBtn.getAttribute('data-id');
                        showDeleteModal(transactionId);
                    @else
                        e.preventDefault();
                        financeShowToast('Anda tidak memiliki izin untuk menghapus transaksi', 'error');
                    @endif
                }
            });

            setupAmountInputs();
        }

        const assetId = document.getElementById('asset-id')?.value || '{{ $asset['asset_id'] ?? "" }}';
        const transactionItems = document.getElementById('transaction-items');
        const filterType = document.getElementById('filter-type');
        const sortBy = document.getElementById('sort-by');

        const expenseTotal = document.getElementById('expense-total');
        const incomeTotal = document.getElementById('income-total');
        const balanceTotal = document.getElementById('balance-total');
        const transactionCount = document.getElementById('transaction-count');

        let currentPage = 1;

        let currentDeleteId = null;

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount).replace('IDR', 'Rp');
        }

        function formatDate(dateString) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function loadTransactions() {
            if (!assetId) {
                showError('Gagal memuat transaksi: ID Aset diperlukan');
                return;
            }

            showLoading();

            if (transactionItems) {
                transactionItems.innerHTML = '';
            }

            const filterValue = filterType?.value || 'all';
            const sortValue = sortBy?.value || 'newest';

            if (filterValue !== 'all') {
                filterType.classList.add('border-[#213268]', 'bg-blue-50');
                const filterLabel = filterValue === 'income' ? 'Pemasukan' : 'Pengeluaran';
                transactionCount.textContent = `Filter: ${filterLabel}`;
            } else {
                filterType.classList.remove('border-[#213268]', 'bg-blue-50');
            }

            if (sortBy) {
                sortBy.classList.add('border-[#213268]', 'bg-blue-50');

                let sortLabel = '';
                switch (sortValue) {
                    case 'newest':
                        sortLabel = 'Terbaru';
                        break;
                    case 'oldest':
                        sortLabel = 'Terlama';
                        break;
                    case 'amount-high':
                        sortLabel = 'Nominal (Tinggi-Rendah)';
                        break;
                    case 'amount-low':
                        sortLabel = 'Nominal (Rendah-Tinggi)';
                        break;
                }
            }

            let queryParams = `?page=${currentPage}`;

            if (filterValue !== 'all') {
                queryParams += `&filter=${filterValue}`;
            }

            if (sortValue) {
                queryParams += `&sort=${sortValue}`;
            }

            if (!assetId) {
                showError('Gagal memuat transaksi: ID Aset diperlukan');
                return;
            }

            fetch(`/asset-transactions/asset/${assetId}${queryParams}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    return response.json().then(data => {
                        return { ...data, httpStatus: response.status };
                    });
                })
                .then(data => {
                    hideLoading();

                    if (data.success) {
                        displayTransactions(data.data.transactions);
                        updateSummary(data.data.summary);
                        if (data.pagination) {
                            updatePagination(data.pagination);
                        }
                    } else {
                        console.error('Error loading transactions:', data);

                        let errorMessage = 'Terjadi kesalahan saat memuat data';
                        if (data.errors) {
                            if (typeof data.errors === 'string') {
                                errorMessage = data.errors;
                            } else if (typeof data.errors === 'object') {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                if (firstErrorKey) {
                                    const firstError = data.errors[firstErrorKey];
                                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                }
                            }
                        }

                        showError(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Network error loading transactions:', error);
                    showError('Gagal memuat transaksi. Terjadi kesalahan jaringan.');
                });
        }

        function displayTransactions(transactions) {
            if (!transactions || transactions.length === 0) {
                transactionItems.innerHTML = `
                <tr>
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        Belum ada transaksi keuangan untuk aset ini.
                    </td>
                </tr>
            `;
                return;
            }

            let html = '';
            transactions.forEach(transaction => {
                const isIncome = transaction.type === 'income';
                const typeClass = isIncome ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const typeLabel = isIncome ? 'Pemasukan' : 'Pengeluaran';
                const iconBg = isIncome ? 'bg-[#27AE60]' : 'bg-[#E74C3C]';
                const iconPath = isIncome
                    ? 'M5 10l7-7m0 0l7 7m-7-7v18'
                    : 'M19 14l-7 7m0 0l-7-7m7 7V3';

                html += `
                <tr class="hover:bg-gray-50 transition-colors" data-id="${transaction.transaction_id}">
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-3">
                                <div class="${iconBg} p-2 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
                                    </svg>
                                </div>
                            </div>
                            <span>${formatDate(transaction.transaction_date)}</span>
                        </div>
                    </td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                        <span class="px-2 py-1 ${typeClass} rounded-full text-xs font-medium">${typeLabel}</span>
                    </td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-right font-semibold">${formatCurrency(transaction.amount)}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4] truncate">${transaction.description || '-'}</td>
                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center space-x-2">
                            @if(hasPermission('asset:transaction:edit'))
                                <button class="edit-transaction p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors" title="Edit" data-id="${transaction.transaction_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            @endif
                            @if(hasPermission('asset:transaction:delete'))
                                <button class="delete-transaction p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors" title="Hapus" data-id="${transaction.transaction_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            `;
            });

            transactionItems.innerHTML = html;
        }

        function updateSummary(summary) {
            if (summary) {
                expenseTotal.textContent = formatCurrency(summary.expense.total);
                incomeTotal.textContent = formatCurrency(summary.income.total);
                balanceTotal.textContent = formatCurrency(summary.balance);

                const totalCount = summary.expense.count + summary.income.count;
                transactionCount.textContent = `Total Transaksi: ${totalCount}`;
            }
        }

        function updatePagination(pagination) {
        }

        window.editTransaction = function (id) {
            const editFormError = document.getElementById('edit-form-error');
            if (editFormError) editFormError.classList.add('hidden');

            fetch(`/asset-transactions/${id}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const transaction = data.data;

                        const form = document.getElementById('editTransactionForm');
                        form.action = `/asset-transactions/${transaction.transaction_id}`;

                        const newForm = form.cloneNode(true);
                        form.parentNode.replaceChild(newForm, form);

                        setupEditForm(newForm);

                        document.getElementById('edit-transaction-id').value = transaction.transaction_id;
                        document.getElementById('edit-asset-id').value = transaction.asset_id;

                        if (transaction.type === 'income') {
                            document.getElementById('edit-type-income').checked = true;
                        } else {
                            document.getElementById('edit-type-expense').checked = true;
                        }

                        document.getElementById('edit-transaction-date').value = transaction.transaction_date;
                        document.getElementById('edit-transaction-amount').value = parseFloat(transaction.amount).toLocaleString('id-ID');
                        document.getElementById('edit-transaction-description').value = transaction.description || '';

                        const modal = document.getElementById('editTransactionModal');
                        const content = document.getElementById('editTransactionModalContent');
                        openModal(modal, content);
                    } else {
                        let errorMessage = 'Gagal memuat detail transaksi';
                        if (data.errors) {
                            if (typeof data.errors === 'string') {
                                errorMessage = data.errors;
                            } else if (typeof data.errors === 'object') {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                if (firstErrorKey) {
                                    const firstError = data.errors[firstErrorKey];
                                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                }
                            }
                        }

                        showToast(errorMessage, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction details:', error);
                    showToast('Error: ' + error.message, 'error');
                });
        }

        function showDeleteModal(id) {
            currentDeleteId = id;
            const modal = document.getElementById('deleteTransactionModal');
            const content = document.getElementById('deleteTransactionModalContent');
            const form = document.getElementById('deleteTransactionForm');

            form.action = `/asset-transactions/${id}`;

            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);

            const deleteBtn = newForm.querySelector('#deleteTransactionSubmitBtn');
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = 'Hapus';
            }

            setupDeleteForm(newForm);

            openModal(modal, content);
        }

        function setupEditForm(form) {
            const editSubmitBtn = document.getElementById('editTransactionSubmitBtn');

            if (editSubmitBtn) {
                const newEditBtn = editSubmitBtn.cloneNode(true);
                editSubmitBtn.parentNode.replaceChild(newEditBtn, editSubmitBtn);

                newEditBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    const type = form.querySelector('input[name="type"]:checked')?.value;
                    const date = document.getElementById('edit-transaction-date')?.value;
                    let amount = document.getElementById('edit-transaction-amount')?.value;

                    if (!type) {
                        financeShowToast('Please select a transaction type', 'error');
                        return;
                    }

                    if (!date) {
                        financeShowToast('Please enter a transaction date', 'error');
                        return;
                    }

                    if (!amount) {
                        financeShowToast('Please enter an amount', 'error');
                        return;
                    }

                    amount = amount.replace(/[^\d,]/g, '');
                    amount = amount.replace(/,/g, '.');
                    amount = parseFloat(amount);

                    if (isNaN(amount)) {
                        financeShowToast('Please enter a valid amount', 'error');
                        return;
                    }

                    this.disabled = true;
                    const originalBtnText = this.innerHTML;
                    this.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                    const formData = new FormData(form);
                    formData.set('amount', amount);

                    const assetId = formData.get('asset_id');
                    if (!assetId) {
                        financeShowToast('ID Aset diperlukan untuk mengedit transaksi', 'error');
                        this.disabled = false;
                        this.innerHTML = originalBtnText;
                        return;
                    }

                    const jsonData = {
                        asset_id: parseInt(formData.get('asset_id')),
                        type: formData.get('type'),
                        amount: parseFloat(formData.get('amount')),
                        transaction_date: formData.get('transaction_date')
                    };

                    const description = formData.get('description');
                    if (description && description.trim() !== '') {
                        jsonData.description = description.trim();
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(form.action, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(jsonData)
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                financeShowToast('Transaksi berhasil diperbarui', 'success');

                                const modal = document.getElementById('editTransactionModal');
                                const content = document.getElementById('editTransactionModalContent');
                                closeModal(modal, content);

                                loadTransactions();
                            } else {
                                let errorMessage = 'Gagal memperbarui transaksi';
                                if (result.errors) {
                                    if (typeof result.errors === 'string') {
                                        errorMessage = result.errors;
                                    } else if (typeof result.errors === 'object') {
                                        const firstErrorKey = Object.keys(result.errors)[0];
                                        if (firstErrorKey) {
                                            const firstError = result.errors[firstErrorKey];
                                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                        }
                                    }
                                }

                                financeShowToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            financeShowToast('Terjadi kesalahan saat memperbarui transaksi', 'error');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = originalBtnText;
                        });
                });
            }
        }

        setupEditForm(document.getElementById('editTransactionForm'));

        function setupDeleteForm(form) {
            const deleteSubmitBtn = document.getElementById('deleteTransactionSubmitBtn');

            if (deleteSubmitBtn) {
                const newDeleteBtn = deleteSubmitBtn.cloneNode(true);
                deleteSubmitBtn.parentNode.replaceChild(newDeleteBtn, deleteSubmitBtn);

                newDeleteBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!currentDeleteId) {
                        showToast('No transaction selected for deletion', 'error');
                        return;
                    }

                    this.disabled = true;
                    const originalBtnText = this.innerHTML;
                    this.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const deleteBtn = this;

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                        .then(response => response.json())
                        .then(result => {
                            const modal = document.getElementById('deleteTransactionModal');
                            const content = document.getElementById('deleteTransactionModalContent');
                            closeModal(modal, content);

                            if (result.success) {
                                financeShowToast('Transaksi berhasil dihapus', 'success');
                                loadTransactions();
                            } else {
                                let errorMessage = 'Gagal menghapus transaksi';
                                if (result.errors) {
                                    if (typeof result.errors === 'string') {
                                        errorMessage = result.errors;
                                    } else if (typeof result.errors === 'object') {
                                        const firstErrorKey = Object.keys(result.errors)[0];
                                        if (firstErrorKey) {
                                            const firstError = result.errors[firstErrorKey];
                                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                        }
                                    }
                                }

                                financeShowToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            financeShowToast('Terjadi kesalahan saat menghapus transaksi', 'error');
                        })
                        .finally(() => {
                            deleteBtn.disabled = false;
                            deleteBtn.innerHTML = originalBtnText;

                            currentDeleteId = null;
                        });
                });
            }
        }

        setupDeleteForm(document.getElementById('deleteTransactionForm'));

        if (filterType) {
            filterType.addEventListener('change', function () {
                currentPage = 1;
                loadTransactions();
            });
        }

        if (sortBy) {
            sortBy.addEventListener('change', loadTransactions);
        }

        setupEventListeners();

        loadTransactions();

        document.getElementById('addTransactionSubmitBtn')?.addEventListener('click', function (e) {
            e.preventDefault();

            const form = document.getElementById('addTransactionForm');
            const formData = new FormData(form);
            const assetId = formData.get('asset_id');
            if (!assetId) {
                financeShowToast('ID Aset diperlukan untuk menambahkan transaksi', 'error');
                return;
            }

            const type = formData.get('type');
            const date = formData.get('transaction_date');
            let amount = formData.get('amount');

            if (!type) {
                financeShowToast('Pilih tipe transaksi', 'error');
                return;
            }

            if (!date) {
                financeShowToast('Masukkan tanggal transaksi', 'error');
                return;
            }

            if (!amount) {
                financeShowToast('Masukkan nominal transaksi', 'error');
                return;
            }

            amount = amount.replace(/[^\d,]/g, '');
            amount = amount.replace(/,/g, '.');
            amount = parseFloat(amount);

            if (isNaN(amount)) {
                financeShowToast('Masukkan nominal yang valid', 'error');
                return;
            }

            const jsonData = {
                asset_id: parseInt(formData.get('asset_id')),
                type: formData.get('type'),
                amount: amount,
                transaction_date: formData.get('transaction_date')
            };

            const description = formData.get('description');
            if (description && description.trim() !== '') {
                jsonData.description = description.trim();
            }

            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = document.getElementById('addTransactionModal');
                        const modalContent = document.getElementById('addTransactionModalContent');
                        closeModal(modal, modalContent);

                        form.reset();

                        financeShowToast('Transaksi berhasil ditambahkan', 'success');

                        loadTransactions();
                    } else {
                        let errorMessage = 'Gagal menambahkan transaksi';
                        if (data.errors) {
                            if (typeof data.errors === 'string') {
                                errorMessage = data.errors;
                            } else if (typeof data.errors === 'object') {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                if (firstErrorKey) {
                                    const firstError = data.errors[firstErrorKey];
                                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                }
                            }
                        }

                        financeShowToast(errorMessage, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    financeShowToast('Terjadi kesalahan saat menambahkan transaksi', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
        });

        function setupAmountInputs() {
            const addAmountInput = document.getElementById('transaction-amount');
            if (addAmountInput) {
                addAmountInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/[^\d]/g, '');
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID');
                    }
                    this.value = value;
                });
            }

            const editAmountInput = document.getElementById('edit-transaction-amount');
            if (editAmountInput) {
                editAmountInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/[^\d]/g, '');
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID');
                    }
                    this.value = value;
                });
            }
        }

        function formatAmount(value) {
            value = value.replace(/[^\d,]/g, '');
            value = value.replace(/,/g, '.');
            return parseFloat(value) || 0;
        }
    });
</script>
