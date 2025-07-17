<!-- Task Reminders Modal -->
<div id="reminders-backdrop" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity"></div>

<div id="reminders-popup"
    class="hidden fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[95%] max-w-[900px] bg-white rounded-lg shadow-2xl z-50 max-h-[90vh] overflow-hidden border border-gray-100">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
        <h3 class="text-base sm:text-lg font-semibold text-[#232D42] flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#213268]" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pengingat Tugas
        </h3>
        <div class="flex items-center gap-3">
            <!-- Filter for asset type -->
            <select id="asset-type-filter"
                class="text-xs sm:text-sm border border-gray-300 rounded-md px-3 py-1.5 bg-gray-50 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-[#213268] focus:border-transparent">
                <option value="all">Semua Jenis</option>
                <option value="medical">Medis</option>
                <option value="non_medical">Non Medis</option>
            </select>
            <!-- Refresh button -->
            <button id="refresh-reminders"
                class="text-[#213268] hover:text-[#182451] p-2 rounded-full hover:bg-blue-50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#213268]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
            <!-- Close button -->
            <button id="close-reminders"
                class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Reminders Tabs -->
    <div class="flex border-b border-gray-200 sticky top-[60px] bg-white z-10">
        <button id="calibration-tab"
            class="flex-1 py-3 text-sm font-medium text-center text-[#213268] border-b-2 border-[#213268] transition-colors hover:bg-blue-50">
            Kalibrasi
        </button>
        <button id="maintenance-tab"
            class="flex-1 py-3 text-sm font-medium text-center text-gray-500 hover:text-[#213268] transition-colors hover:bg-gray-50">
            Pemeliharaan
        </button>
    </div>

    <div id="reminders-content" class="max-h-[calc(90vh-130px)] overflow-y-auto overflow-x-hidden custom-scrollbar"
        style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">

        <!-- Category tabs - Horizontal scroll on mobile -->
        <div class="overflow-x-auto sticky top-0 z-10 bg-gray-50 shadow-sm">
            <div id="reminders-categories-tabs" class="flex border-b border-gray-200 bg-gray-50 px-2 min-w-max">
                <button data-category="today"
                    class="category-tab active px-3 py-2.5 text-xs font-medium text-center text-[#213268] border-b-2 border-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>Hari Ini</span>
                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 hidden today-count"></span>
                </button>
                <button data-category="one_to_fourteen_days"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>1-14 Hari</span>
                    <span
                        class="bg-orange-500 text-white text-xs rounded-full px-2 py-0.5 hidden one_to_fourteen_days-count"></span>
                </button>
                <button data-category="fifteen_to_thirty_days"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>15-30 Hari</span>
                    <span
                        class="bg-yellow-500 text-white text-xs rounded-full px-2 py-0.5 hidden fifteen_to_thirty_days-count"></span>
                </button>
                <button data-category="one_to_two_months"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>1-2 Bulan</span>
                    <span
                        class="bg-blue-500 text-white text-xs rounded-full px-2 py-0.5 hidden one_to_two_months-count"></span>
                </button>
                <button data-category="two_to_three_months"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>2-3 Bulan</span>
                    <span
                        class="bg-indigo-500 text-white text-xs rounded-full px-2 py-0.5 hidden two_to_three_months-count"></span>
                </button>
                <button data-category="more_than_3_months"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>> 3 Bulan</span>
                    <span
                        class="bg-purple-500 text-white text-xs rounded-full px-2 py-0.5 hidden more_than_3_months-count"></span>
                </button>
                <button data-category="missed"
                    class="category-tab px-3 py-2.5 text-xs font-medium text-center text-gray-500 hover:text-[#213268] flex items-center gap-1.5 whitespace-nowrap transition-all">
                    <span>Terlewat</span>
                    <span class="bg-gray-500 text-white text-xs rounded-full px-2 py-0.5 hidden missed-count"></span>
                </button>
            </div>
        </div>

        <div id="reminders-table-container" class="p-2 sm:p-4">
            <!-- Loading indicator -->
            <div id="reminders-loading" class="py-12 text-center">
                <div
                    class="animate-spin mx-auto h-10 w-10 border-4 border-t-transparent border-[#213268] rounded-full mb-4">
                </div>
                <p class="text-gray-500 font-medium">Memuat data...</p>
            </div>

            <!-- Table for Calibration -->
            <div id="calibration-table" class="hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama Aset</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kode Aset</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Merek</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Lokasi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Jadwal</th>
                            </tr>
                        </thead>
                        <tbody id="calibration-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div id="calibration-load-more" class="hidden py-4 text-center">
                    <button
                        class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 shadow-sm">
                        Muat Lebih Banyak
                    </button>
                </div>
            </div>

            <!-- Table for Maintenance -->
            <div id="maintenance-table" class="hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama Aset</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kode Aset</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Merek</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Lokasi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Jadwal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Penanggung Jawab</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Interval</th>
                            </tr>
                        </thead>
                        <tbody id="maintenance-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div id="maintenance-load-more" class="hidden py-4 text-center">
                    <button
                        class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 shadow-sm">
                        Muat Lebih Banyak
                    </button>
                </div>
            </div>

            <!-- Mobile view tables with cards -->
            <div id="calibration-mobile-view" class="sm:hidden hidden space-y-3 px-1">
                <!-- Mobile card items will be inserted here -->
            </div>

            <div id="maintenance-mobile-view" class="sm:hidden hidden space-y-3 px-1">
                <!-- Mobile card items will be inserted here -->
            </div>

            <!-- Empty state -->
            <div id="reminders-empty" class="hidden py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 font-medium mb-1">Tidak ada pengingat untuk ditampilkan</p>
                <p class="text-gray-400 text-sm">Silakan pilih kategori atau filter lainnya</p>
            </div>

            <!-- Error state -->
            <div id="reminders-error" class="hidden py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-500 font-medium mb-1">Gagal memuat data pengingat</p>
                <p class="text-gray-400 text-sm mb-3">Silakan coba lagi nanti</p>
                <button id="reminders-retry"
                    class="px-4 py-2 bg-[#213268] text-white text-sm rounded-md hover:bg-[#182451] transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-[#213268] focus:ring-offset-2">
                    Coba lagi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles for reminders popup */
    #reminders-popup {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #reminders-popup.hidden {
        opacity: 0;
        transform: translate(-50%, -45%);
    }

    #reminders-popup:not(.hidden) {
        animation: modal-in 0.3s ease-out forwards;
    }

    #reminders-backdrop:not(.hidden) {
        animation: fade-in 0.2s ease-out forwards;
    }

    @keyframes modal-in {
        0% {
            opacity: 0;
            transform: translate(-50%, -45%);
        }

        100% {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    @keyframes fade-in {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    /* Make tables look better */
    #calibration-table tbody tr:hover,
    #maintenance-table tbody tr:hover {
        background-color: #f9fafb;
    }

    #calibration-table td,
    #maintenance-table td {
        transition: background-color 0.15s ease-in-out;
    }

    /* Make category tabs interactive */
    .category-tab {
        position: relative;
        transition: all 0.2s ease;
    }

    .category-tab:hover:not(.active) {
        background-color: #f9fafb;
    }

    .category-tab.active {
        font-weight: 600;
    }

    @media (max-width: 640px) {
        #reminders-popup {
            width: 95% !important;
            max-height: 90vh !important;
        }

        .reminder-card {
            animation: fadeIn 0.3s ease-in-out;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .reminder-card:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .reminder-card .asset-name {
            font-weight: 600;
            font-size: 14px;
            color: #213268;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .reminder-card .schedule-date {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 4px;
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: 500;
        }

        .reminder-card .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .reminder-card .info-row:last-child {
            border-bottom: none;
        }

        .reminder-card .info-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 500;
        }

        .reminder-card .info-value {
            color: #111827;
            font-size: 12px;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Category tabs styling for mobile */
        .category-tab {
            padding-left: 12px;
            padding-right: 12px;
        }

        /* Make the modal take up more screen space on very small devices */
        @media (max-width: 375px) {
            #reminders-popup {
                width: 98% !important;
                max-height: 92vh !important;
            }
        }
    }

    /* Improve scrollbar for better touch experience */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #213268;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #182451;
    }
</style>