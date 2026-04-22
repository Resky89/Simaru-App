<x-modal id="importBrandModal" title="IMPORT MERK" maxWidth="sm:max-w-[700px]">
    <!-- Step 1: File Selection -->
    <div id="import-brand-step-1" class="block">
        <div class="p-6">
            <div class="space-y-6">
                <!-- Import Instructions -->
                <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                    <p class="font-medium text-blue-600 mb-2">Petunjuk Import:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Gunakan format template Excel untuk mengimpor</li>
                        <li>Kolom yang diperlukan: Nama Merk</li>
                        <li>Maksimal 100 data per import</li>
                        <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                    </ul>
                    <div class="mt-3 flex justify-end">
                        <a href="{{ asset('docs/ImportBrandTemplate.xlsx') }}" download
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                </path>
                            </svg>
                            Unduh Template
                        </a>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#213268]">File Excel</label>
                    <div
                        class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                        <!-- File preview -->
                        <div id="brand-excel-file-name" class="mt-2 mb-4 w-full hidden">
                            <div
                                class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-2"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span id="brand-file-name-text"
                                        class="text-sm text-gray-700 truncate"></span>
                                    <button type="button" id="remove-brand-excel"
                                        class="ml-auto text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span
                                    class="text-[#213268] font-semibold">telusuri file</span></p>
                            <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv</p>
                            <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                untuk memilih file</p>
                        </div>
                        <input type="file" id="brand_excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                </div>

                <!-- Error Message -->
                <div id="brand-excel-error" class="hidden text-red-500 text-sm"></div>

                <!-- Loading Indicator -->
                <div id="brand-excel-loading" class="hidden text-center py-2">
                    <div
                        class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin">
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Memproses data Excel...</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button"
                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                        Batal
                    </button>
                    <button type="button" id="brand-preview-btn" disabled
                        class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Pratinjau Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Import Progress -->
    <div id="import-brand-step-2" class="hidden">
        <div class="p-6">
            <div class="space-y-6">
                <!-- Preview Header -->
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                    <span class="text-sm text-gray-500" id="brand-preview-count">0 item ditemukan</span>
                </div>

                <!-- Preview Table -->
                <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-[#213268] text-white">
                            <tr>
                                <th class="p-3 text-left text-xs font-semibold">No</th>
                                <th class="p-3 text-left text-xs font-semibold">Nama Merk</th>
                            </tr>
                        </thead>
                        <tbody id="brand-preview-table-body">
                            <!-- Preview data will be inserted here -->
                        </tbody>
                    </table>
                </div>

                <!-- Warning/Error Messages -->
                <div id="brand-preview-warnings"
                    class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                    <p class="font-medium mb-2">Peringatan:</p>
                    <ul class="list-disc pl-5" id="brand-warning-list">
                        <!-- Warning messages will be inserted here -->
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="button" id="brand-back-to-upload-btn"
                        class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                        Kembali
                    </button>
                    <form action="{{ route('brands.import') }}" method="POST" id="brand-import-form"
                        class="w-2/3" data-no-loading enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="excel_data" id="brand_excel_data">
                        <button type="submit" id="brand-import-btn"
                            class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                            Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-modal>
