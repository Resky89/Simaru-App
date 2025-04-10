<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">FINANCIAL TRANSACTIONS</h2>
        <button type="button" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            TRANSACTION
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
                <span class="text-sm text-gray-600 w-40">Sold</span>
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
                <span class="text-sm text-gray-600 w-40">service maintenance</span>
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
            <span class="text-sm font-medium">Total Expenses</span>
            <span class="text-sm font-semibold">Rp 500.000,00</span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-sm font-medium">Total Income</span>
            <span class="text-sm font-semibold">Rp 10.000.000,00</span>
        </div>
        <div class="flex justify-between py-2 border-t border-gray-200 pt-3">
            <span class="text-sm font-bold">Balance</span>
            <span class="text-sm font-bold">Rp 9.500.000,00</span>
        </div>
    </div>
</div>
