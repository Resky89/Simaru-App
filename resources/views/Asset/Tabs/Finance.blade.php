<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">TRANSAKSI KEUANGAN</h2>
        <button id="addTransactionBtn" type="button" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            TAMBAH TRANSAKSI
        </button>
    </div>

    <!-- Transaction Filter/Sort -->
    <div class="flex items-center justify-between mb-4 bg-gray-50 p-3 rounded-md">
        <div class="text-sm font-medium text-gray-700" id="transaction-count">Total Transaksi: 0</div>
        <div class="flex space-x-2">
            <select id="filter-type" class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                <option value="all">Semua Tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
            <select id="sort-by" class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="amount-high">Nominal (Tinggi-Rendah)</option>
                <option value="amount-low">Nominal (Rendah-Tinggi)</option>
            </select>
        </div>
    </div>

    <!-- Transactions List with better headers -->
    <div class="overflow-x-auto -mx-3 sm:mx-0 rounded-md">
        <table class="w-full min-w-[500px] border-collapse">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-3/12">Tanggal</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-2/12">Tipe</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-right w-3/12">Nominal</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left w-3/12">Keterangan</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-center w-1/12">Aksi</th>
                </tr>
            </thead>
            <tbody id="transaction-items">
                <tr class="transaction-loading-row">
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin h-5 w-5 text-[#213268] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat transaksi...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary in cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="p-4 bg-red-50 border border-red-100 rounded-lg">
            <div class="flex items-center mb-2">
                <div class="mr-2 p-2 bg-red-500 text-white rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Total Pengeluaran</h3>
            </div>
            <p id="expense-total" class="text-xl font-bold text-red-600">Rp 0,00</p>
        </div>

        <div class="p-4 bg-green-50 border border-green-100 rounded-lg">
            <div class="flex items-center mb-2">
                <div class="mr-2 p-2 bg-green-500 text-white rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Total Pemasukan</h3>
            </div>
            <p id="income-total" class="text-xl font-bold text-green-600">Rp 0,00</p>
        </div>

        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
            <div class="flex items-center mb-2">
                <div class="mr-2 p-2 bg-blue-500 text-white rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700">Saldo Total</h3>
            </div>
            <p id="balance-total" class="text-xl font-bold text-blue-600">Rp 0,00</p>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div id="addTransactionModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="addTransactionModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH TRANSAKSI BARU</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="addTransactionModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="addTransactionForm">
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Hidden asset_id field -->
                                <input type="hidden" id="asset-id" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                <!-- Type Input with better options -->
                                <div class="space-y-2">
                                    <label for="transaction-type" class="block text-base font-semibold text-[#666666]">Tipe <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="type" value="expense" class="mr-2 text-[#213268] focus:ring-[#213268]">
                                            <div class="flex items-center">
                                                <div class="p-1.5 bg-red-100 rounded-md mr-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                    </svg>
                                                </div>
                                                <span>Pengeluaran</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="type" value="income" class="mr-2 text-[#213268] focus:ring-[#213268]">
                                            <div class="flex items-center">
                                                <div class="p-1.5 bg-green-100 rounded-md mr-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                    </svg>
                                                </div>
                                                <span>Pemasukan</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Date Input -->
                                <div class="space-y-2">
                                    <label for="transaction-date" class="block text-base font-semibold text-[#666666]">Tanggal <span class="text-red-500">*</span></label>
                                    <input type="date" id="transaction-date" name="transaction_date" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                </div>

                                <!-- Amount Input with currency prefix -->
                                <div class="space-y-2">
                                    <label for="transaction-amount" class="block text-base font-semibold text-[#666666]">Nominal <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="text" id="transaction-amount" name="amount" required placeholder="0,00"
                                            class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- Notes Input -->
                                <div class="space-y-2">
                                    <label for="transaction-description" class="block text-base font-semibold text-[#666666]">Keterangan</label>
                                    <textarea id="transaction-description" name="description" rows="3"
                                        class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Keterangan transaksi"></textarea>
                                </div>

                                <!-- Error message -->
                                <div id="form-error" class="text-red-500 text-sm hidden"></div>

                                <!-- Save Button -->
                                <div class="pt-4 flex gap-4">
                                    <button type="submit" id="addTransactionSubmitBtn"
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

    <!-- Edit Transaction Modal -->
    <div id="editTransactionModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="editTransactionModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT TRANSAKSI</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="editTransactionModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="editTransactionForm">
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Hidden fields -->
                                <input type="hidden" id="edit-transaction-id" name="transaction_id">
                                <input type="hidden" id="edit-asset-id" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                <!-- Type Input -->
                                <div class="space-y-2">
                                    <label for="edit-transaction-type" class="block text-base font-semibold text-[#666666]">Tipe <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="type" value="expense" id="edit-type-expense" class="mr-2 text-[#213268] focus:ring-[#213268]">
                                            <div class="flex items-center">
                                                <div class="p-1.5 bg-red-100 rounded-md mr-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                    </svg>
                                                </div>
                                                <span>Pengeluaran</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="type" value="income" id="edit-type-income" class="mr-2 text-[#213268] focus:ring-[#213268]">
                                            <div class="flex items-center">
                                                <div class="p-1.5 bg-green-100 rounded-md mr-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                    </svg>
                                                </div>
                                                <span>Pemasukan</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Date Input -->
                                <div class="space-y-2">
                                    <label for="edit-transaction-date" class="block text-base font-semibold text-[#666666]">Tanggal <span class="text-red-500">*</span></label>
                                    <input type="date" id="edit-transaction-date" name="transaction_date" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                </div>

                                <!-- Amount Input -->
                                <div class="space-y-2">
                                    <label for="edit-transaction-amount" class="block text-base font-semibold text-[#666666]">Nominal <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="text" id="edit-transaction-amount" name="amount" required placeholder="0,00"
                                            class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- Notes Input -->
                                <div class="space-y-2">
                                    <label for="edit-transaction-description" class="block text-base font-semibold text-[#666666]">Keterangan</label>
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

    <!-- Delete Transaction Modal -->
    <div id="deleteTransactionModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="deleteTransactionModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS TRANSAKSI</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="deleteTransactionModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <form id="deleteTransactionForm">
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200" data-modal="deleteTransactionModal">
                                        Batal
                                    </button>
                                    <button type="submit" id="deleteTransactionSubmitBtn" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
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
</div>

<!-- Add JavaScript for Transaction functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const addTransactionBtn = document.getElementById('addTransactionBtn');
    const addTransactionModal = document.getElementById('addTransactionModal');
    const addTransactionModalContent = document.getElementById('addTransactionModalContent');
    const editTransactionModal = document.getElementById('editTransactionModal');
    const editTransactionModalContent = document.getElementById('editTransactionModalContent');
    const deleteTransactionModal = document.getElementById('deleteTransactionModal');
    const deleteTransactionModalContent = document.getElementById('deleteTransactionModalContent');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const transactionForm = document.getElementById('addTransactionForm');
    const editTransactionForm = document.getElementById('editTransactionForm');
    const deleteTransactionForm = document.getElementById('deleteTransactionForm');
    const transactionItems = document.getElementById('transaction-items');
    const formError = document.getElementById('form-error');
    const filterType = document.getElementById('filter-type');
    const sortBy = document.getElementById('sort-by');

    // Finance summary elements
    const expenseTotal = document.getElementById('expense-total');
    const incomeTotal = document.getElementById('income-total');
    const balanceTotal = document.getElementById('balance-total');
    const transactionCount = document.getElementById('transaction-count');

    // Asset ID
    const assetId = document.getElementById('asset-id').value;

    // Current page for pagination
    let currentPage = 1;

    // Current transaction being deleted
    let currentDeleteId = null;

    // Format currency function
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount).replace('IDR', 'Rp');
    }

    // Format date as DD MMM YYYY
    function formatDate(dateString) {
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Open modal function
    function openModal(modal, content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }, 10);
    }

    // Close modal function
    function closeModal(modal, content) {
        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
        content.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Load transactions function
    function loadTransactions() {
        // Show loading state
        transactionItems.innerHTML = `
            <tr class="transaction-loading-row">
                <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                    <div class="flex justify-center items-center">
                        <svg class="animate-spin h-5 w-5 text-[#213268] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memuat transaksi...
                    </div>
                </td>
            </tr>
        `;

        // Get filter and sort values
        const filterValue = filterType.value;
        const sortValue = sortBy.value;

        // Build query parameters
        let queryParams = `?page=${currentPage}`;
        if (filterValue !== 'all') {
            queryParams += `&filter=${filterValue}`;
        }
        if (sortValue) {
            queryParams += `&sort=${sortValue}`;
        }

        // Fetch transactions from API
        fetch(`/asset-transactions/asset/${assetId}${queryParams}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayTransactions(data.data.transactions);
                    updateSummary(data.data.summary);
                    updatePagination(data.pagination);
                } else {
                    showError('Failed to load transactions: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching transactions:', error);
                transactionItems.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                            Gagal memuat transaksi. Silakan coba lagi.
                        </td>
                    </tr>
                `;
            });
    }

    // Display transactions function
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

        // Build transactions HTML
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
                            <button class="edit-transaction text-[#3D3D3D] hover:text-[#213268] focus:outline-none" title="Edit" data-id="${transaction.transaction_id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button class="delete-transaction text-[#3D3D3D] hover:text-red-500 focus:outline-none" title="Hapus" data-id="${transaction.transaction_id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        transactionItems.innerHTML = html;

        // Add event listeners to edit/delete buttons
        document.querySelectorAll('.edit-transaction').forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-id');
                editTransaction(transactionId);
            });
        });

        document.querySelectorAll('.delete-transaction').forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-id');
                showDeleteModal(transactionId);
            });
        });
    }

    // Update summary function
    function updateSummary(summary) {
        if (summary) {
            expenseTotal.textContent = formatCurrency(summary.expense.total);
            incomeTotal.textContent = formatCurrency(summary.income.total);
            balanceTotal.textContent = formatCurrency(summary.balance);

            const totalCount = summary.expense.count + summary.income.count;
            transactionCount.textContent = `Total Transaksi: ${totalCount}`;
        }
    }

    // Update pagination function
    function updatePagination(pagination) {
        // Implement pagination UI if needed
    }

    // Show error function
    function showError(message) {
        formError.textContent = message;
        formError.classList.remove('hidden');

        setTimeout(() => {
            formError.classList.add('hidden');
        }, 5000);
    }

    // Edit transaction function
    function editTransaction(id) {
        // Show loading state
        const editFormError = document.getElementById('edit-form-error');
        editFormError.classList.add('hidden');

        // Get transaction details
        fetch(`/asset-transactions/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const transaction = data.data;

                    // Populate form fields
                    document.getElementById('edit-transaction-id').value = transaction.transaction_id;
                    document.getElementById('edit-asset-id').value = transaction.asset_id;

                    // Set transaction type
                    if (transaction.type === 'income') {
                        document.getElementById('edit-type-income').checked = true;
                    } else {
                        document.getElementById('edit-type-expense').checked = true;
                    }

                    // Set date, amount, and description
                    document.getElementById('edit-transaction-date').value = transaction.transaction_date;
                    document.getElementById('edit-transaction-amount').value = parseFloat(transaction.amount).toLocaleString('id-ID');
                    document.getElementById('edit-transaction-description').value = transaction.description || '';

                    // Open edit modal
                    openModal(editTransactionModal, editTransactionModalContent);
                } else {
                    showError('Failed to load transaction details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching transaction details:', error);
                showError('An error occurred while loading transaction details');
            });
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentDeleteId = id;
        openModal(deleteTransactionModal, deleteTransactionModalContent);
    }

    // Delete transaction function
    function deleteTransaction(id) {
        fetch(`/asset-transactions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload transactions after delete
                loadTransactions();
            } else {
                showError('Failed to delete transaction: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting transaction:', error);
            showError('An error occurred while deleting the transaction');
        });
    }

    // Add transaction button click
    if (addTransactionBtn) {
        addTransactionBtn.addEventListener('click', function() {
            openModal(addTransactionModal, addTransactionModalContent);
        });
    }

    // Close buttons
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = btn.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            const content = document.getElementById(modalId + 'Content');
            if (modal && content) {
                closeModal(modal, content);
            }
        });
    });

    // Handle transaction form submit
    if (transactionForm) {
        transactionForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            formError.classList.add('hidden');

            // Format amount before submission (remove Rp and commas)
            const amountInput = document.getElementById('transaction-amount');
            let amount = amountInput.value.replace(/[^\d,]/g, '');
            amount = amount.replace(/,/g, '.');

            // Prepare form data
            const formData = new FormData(this);
            formData.set('amount', amount);

            // Convert to JSON
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Submit transaction
            fetch('/asset-transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal after submission
                    closeModal(addTransactionModal, addTransactionModalContent);

                    // Clear form
                    transactionForm.reset();

                    // Reload transactions
                    loadTransactions();
                } else {
                    showError('Failed to create transaction: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error creating transaction:', error);
                showError('An error occurred while creating the transaction');
            });
        });
    }

    // Handle edit transaction form submit
    if (editTransactionForm) {
        editTransactionForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            const editFormError = document.getElementById('edit-form-error');
            editFormError.classList.add('hidden');

            // Get transaction ID
            const transactionId = document.getElementById('edit-transaction-id').value;

            // Format amount before submission (remove Rp and commas)
            const amountInput = document.getElementById('edit-transaction-amount');
            let amount = amountInput.value.replace(/[^\d,]/g, '');
            amount = amount.replace(/,/g, '.');

            // Prepare form data
            const formData = new FormData(this);
            formData.set('amount', amount);

            // Convert to JSON
            const data = {};
            formData.forEach((value, key) => {
                if (value !== '') {
                    data[key] = value;
                }
            });

            // Submit updated transaction
            fetch(`/asset-transactions/${transactionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal after submission
                    closeModal(editTransactionModal, editTransactionModalContent);

                    // Reload transactions
                    loadTransactions();
                } else {
                    editFormError.textContent = 'Failed to update transaction: ' + data.message;
                    editFormError.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error updating transaction:', error);
                editFormError.textContent = 'An error occurred while updating the transaction';
                editFormError.classList.remove('hidden');
            });
        });
    }

    // Handle delete transaction form submit
    if (deleteTransactionForm) {
        deleteTransactionForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (currentDeleteId) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;

                deleteTransaction(currentDeleteId);

                // Close modal after submission
                closeModal(deleteTransactionModal, deleteTransactionModalContent);

                // Reset button state
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }, 300);
            }
        });
    }

    // Filter and sort change handlers
    if (filterType) {
        filterType.addEventListener('change', function() {
            currentPage = 1; // Reset to first page when filter changes
            loadTransactions();
        });
    }

    if (sortBy) {
        sortBy.addEventListener('change', loadTransactions);
    }

    // Initialize - load transactions on page load
    loadTransactions();
});
</script>
