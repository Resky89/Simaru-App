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

    <!-- Transaction Filter/Sort (Optional) -->
    <div class="flex items-center justify-between mb-4 bg-gray-50 p-3 rounded-md">
        <div class="text-sm font-medium text-gray-700">Total Transaksi: 2</div>
        <div class="flex space-x-2">
            <select class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                <option>Semua Tipe</option>
                <option>Pemasukan</option>
                <option>Pengeluaran</option>
            </select>
            <select class="text-sm border border-gray-300 rounded-md px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-[#213268]">
                <option>Terbaru</option>
                <option>Terlama</option>
                <option>Nominal (Tinggi-Rendah)</option>
                <option>Nominal (Rendah-Tinggi)</option>
            </select>
        </div>
    </div>

    <!-- Transactions List with better headers -->
    <div class="border rounded-lg overflow-hidden mb-6">
        <!-- Header -->
        <div class="bg-gray-100 p-3 grid grid-cols-12 gap-2 text-sm font-semibold text-gray-700 border-b">
            <div class="col-span-3">Tanggal</div>
            <div class="col-span-2">Tipe</div>
            <div class="col-span-3 text-right">Nominal</div>
            <div class="col-span-3">Keterangan</div>
            <div class="col-span-1 text-right">Aksi</div>
        </div>

        <!-- Transaction Items -->
        <div class="divide-y divide-gray-100">
            <!-- Transaction Item 1 -->
            <div class="p-3 grid grid-cols-12 gap-2 items-center hover:bg-gray-50 transition-colors">
                <div class="col-span-3 flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        <div class="bg-[#27AE60] p-2 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-sm">10 Apr 2025</span>
                </div>
                <div class="col-span-2">
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Pemasukan</span>
                </div>
                <div class="col-span-3 text-right font-semibold">Rp 10.000.000,00</div>
                <div class="col-span-3 text-sm text-gray-600 truncate">Terjual</div>
                <div class="col-span-1 flex justify-end space-x-2">
                    <button class="text-gray-400 hover:text-blue-500 focus:outline-none" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-red-500 focus:outline-none" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Transaction Item 2 -->
            <div class="p-3 grid grid-cols-12 gap-2 items-center hover:bg-gray-50 transition-colors">
                <div class="col-span-3 flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        <div class="bg-[#E74C3C] p-2 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-sm">07 Apr 2025</span>
                </div>
                <div class="col-span-2">
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Pengeluaran</span>
                </div>
                <div class="col-span-3 text-right font-semibold">Rp 500.000,00</div>
                <div class="col-span-3 text-sm text-gray-600 truncate">Pemeliharaan layanan</div>
                <div class="col-span-1 flex justify-end space-x-2">
                    <button class="text-gray-400 hover:text-blue-500 focus:outline-none" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-red-500 focus:outline-none" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
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
            <p class="text-xl font-bold text-red-600">Rp 500.000,00</p>
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
            <p class="text-xl font-bold text-green-600">Rp 10.000.000,00</p>
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
            <p class="text-xl font-bold text-blue-600">Rp 9.500.000,00</p>
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
                    <div class="bg-[#213268] text-white px-6 py-4">
                        <h2 class="text-xl sm:text-2xl font-semibold">TAMBAH TRANSAKSI BARU</h2>
                    </div>
                    <button class="absolute top-4 right-4 p-2 hover:bg-[#162249] rounded-full transition-colors duration-200 close-modal text-white" data-modal="addTransactionModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Form -->
                    <form id="addTransactionForm">
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Type Input with better options -->
                                <div class="space-y-2">
                                    <label for="transaction-type" class="block text-base font-semibold text-[#666666]">Tipe <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="transaction-type" value="pengeluaran" class="mr-2 text-[#213268] focus:ring-[#213268]">
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
                                            <input type="radio" name="transaction-type" value="pemasukan" class="mr-2 text-[#213268] focus:ring-[#213268]">
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
                                    <input type="date" id="transaction-date" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                </div>

                                <!-- Amount Input with currency prefix -->
                                <div class="space-y-2">
                                    <label for="transaction-amount" class="block text-base font-semibold text-[#666666]">Nominal <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="text" id="transaction-amount" required placeholder="0,00"
                                            class="w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- Category dropdown (Added) -->
                                <div class="space-y-2">
                                    <label for="transaction-category" class="block text-base font-semibold text-[#666666]">Kategori</label>
                                    <select id="transaction-category"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        <option value="">Pilih Kategori</option>
                                        <option value="pemeliharaan">Pemeliharaan</option>
                                        <option value="perbaikan">Perbaikan</option>
                                        <option value="penjualan">Penjualan</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Notes Input -->
                                <div class="space-y-2">
                                    <label for="transaction-description" class="block text-base font-semibold text-[#666666]">Keterangan</label>
                                    <textarea id="transaction-description" rows="3"
                                        class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Keterangan transaksi"></textarea>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4 flex gap-4">
                                    <button type="button" class="close-modal w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200" data-modal="addTransactionModal">
                                        Batal
                                    </button>
                                    <button type="submit" id="addTransactionSubmitBtn"
                                            class="w-2/3 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
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
