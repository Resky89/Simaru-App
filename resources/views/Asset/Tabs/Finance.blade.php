<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">TRANSAKSI KEUANGAN</h2>
        <button id="addTransactionBtn" type="button" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            TRANSAKSI
        </button>
    </div>

    <!-- Transactions List -->
    <div class="space-y-2 mb-6">
        <!-- Transaction Item -->
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md border border-[#EEF1F4]">
            <div class="flex items-center w-1/3">
                <div class="bg-[#0088CC] p-2 rounded-md mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-sm font-medium">10 Apr 2025</span>
            </div>
            <div class="flex items-center justify-end w-2/3">
                <span class="text-sm font-semibold mr-6 w-40 text-right">Rp 10.000.000,00</span>
                <span class="text-sm text-gray-600 w-40">Terjual</span>
                <div class="flex space-x-4">
                    <button class="text-gray-400 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Transaction Item -->
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md border border-[#EEF1F4]">
            <div class="flex items-center w-1/3">
                <div class="bg-[#E74C3C] p-2 rounded-md mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                    </svg>
                </div>
                <span class="text-sm font-medium">07 Apr 2025</span>
            </div>
            <div class="flex items-center justify-end w-2/3">
                <span class="text-sm font-semibold mr-6 w-40 text-right">Rp 500.000,00</span>
                <span class="text-sm text-gray-600 w-40">Pemeliharaan layanan</span>
                <div class="flex space-x-4">
                    <button class="text-gray-400 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="mt-6 space-y-2 border-t border-gray-200 pt-4">
        <div class="flex justify-between py-2">
            <span class="text-sm font-medium">Total Pengeluaran</span>
            <span class="text-sm font-semibold">Rp 500.000,00</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-sm font-medium">Total Pemasukan</span>
            <span class="text-sm font-semibold">Rp 10.000.000,00</span>
        </div>
        <div class="flex justify-between py-2 border-t border-gray-200 pt-3">
            <span class="text-sm font-bold">Saldo</span>
            <span class="text-sm font-bold">Rp 9.500.000,00</span>
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
                                <!-- Type Input -->
                                <div class="space-y-2">
                                    <label for="transaction-type" class="block text-base font-semibold text-[#666666]">Tipe <span class="text-red-500">*</span></label>
                                    <select id="transaction-type" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        <option value="pengeluaran">Pengeluaran</option>
                                        <option value="pemasukan">Pemasukan</option>
                                    </select>
                                </div>

                                <!-- Date Input -->
                                <div class="space-y-2">
                                    <label for="transaction-date" class="block text-base font-semibold text-[#666666]">Tanggal <span class="text-red-500">*</span></label>
                                    <input type="date" id="transaction-date" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                </div>

                                <!-- Amount Input -->
                                <div class="space-y-2">
                                    <label for="transaction-amount" class="block text-base font-semibold text-[#666666]">Nominal (Rp) <span class="text-red-500">*</span></label>
                                    <input type="text" id="transaction-amount" required placeholder="0,00"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                </div>

                                <!-- Notes Input -->
                                <div class="space-y-2">
                                    <label for="transaction-description" class="block text-base font-semibold text-[#666666]">Keterangan</label>
                                    <textarea id="transaction-description" rows="3"
                                        class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Keterangan transaksi"></textarea>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4">
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
</div>

<!-- Add JavaScript for Transaction Modal functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Transaction Modal
    const addTransactionBtn = document.getElementById('addTransactionBtn');
    const addTransactionModal = document.getElementById('addTransactionModal');
    const addTransactionModalContent = document.getElementById('addTransactionModalContent');
    const closeModalBtns = document.querySelectorAll('.close-modal');

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
    const transactionForm = document.getElementById('addTransactionForm');
    if (transactionForm) {
        transactionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form handling code here

            // Close modal after submission
            closeModal(addTransactionModal, addTransactionModalContent);
        });
    }
});
</script>
