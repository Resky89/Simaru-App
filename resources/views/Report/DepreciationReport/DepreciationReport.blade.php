@extends('Layout.app')

@section('title', 'Laporan Penyusutan')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Generate Depreciation Report Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">BUAT LAPORAN PENYUSUTAN</h1>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('report.depreciation') }}" method="GET" id="depreciationFilterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                        <div class="relative">
                            <input type="text" id="building_search" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" placeholder="Cari Gedung" autocomplete="off">
                            <input type="hidden" name="building" id="selected_building_id">

                            <!-- Building Dropdown -->
                            <div id="building_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                            <!-- Loading indicator -->
                            <div id="building_loading" class="flex justify-center py-2">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]"></div>
                                    <span class="ml-2 text-gray-600">Memuat Gedung...</span>
                                </div>
                                <ul id="building_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                        <div class="relative">
                            <input type="text" id="room_search" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" placeholder="Pilih Gedung Telebih Dahulu" autocomplete="off" disabled>
                            <input type="hidden" name="room" id="selected_room_id">

                            <!-- Room Dropdown -->
                            <div id="room_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                <!-- Loading indicator -->
                                <div id="room_loading" class="flex justify-center py-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]"></div>
                                    <span class="ml-2 text-gray-600">Memuat Ruangan...</span>
                                </div>
                                <ul id="room_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                        <div class="relative">
                                <select name="asset_type" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                    <option value="">Semua Tipe Aset</option>
                                    <option value="medical" {{ $asset_type == 'medical' ? 'selected' : '' }}>Medis</option>
                                    <option value="non_medical" {{ $asset_type == 'non_medical' ? 'selected' : '' }}>Non Medis</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <div class="relative">
                                <input type="text" id="subcategory_search" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" placeholder="Pilih Tipe Aset Terlebih Dahulu" autocomplete="off" disabled>
                                <input type="hidden" name="subcategory" id="selected_subcategory">

                                <!-- Subcategory Dropdown -->
                                <div id="subcategory_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                <ul id="subcategory_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berdasarkan Tanggal</label>
                        <div class="relative">
                                <input type="date" name="as_of_date" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268]" value="{{ $as_of_date ?? now()->format('Y-m-d') }}">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Master Aset</label>
                        <div class="relative">
                            <input type="text" id="asset_master_search" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20" placeholder="Cari Berdasarkan Nama Master Aset" autocomplete="off">
                            <input type="hidden" name="search" id="selected_asset_master_id">

                            <!-- Dropdown -->
                            <div id="asset_master_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                <!-- Loading indicator -->
                                <div id="asset_master_loading" class="flex justify-center py-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]"></div>
                                    <span class="ml-2 text-gray-600">Mencari Master Aset...</span>
                                </div>
                                <ul id="asset_master_list" class="max-h-56 overflow-y-auto"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Button -->
                <div class="mt-4">
                    <button type="submit" class="w-full bg-[#213268] text-white py-3 rounded-md hover:bg-[#1a275a] transition-colors">
                        Membuat Laporan
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Depreciation Report Results Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">LAPORAN PENYUSUTAN</h1>

                    <!-- Controls -->
                    <div class="flex items-center gap-3">
                        <!-- Toggle switch -->
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">Nilai</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="percentageToggle" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#213268] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]"></div>
                                <span class="ml-2 text-sm text-gray-600">Persen</span>
                            </label>
                        </div>

                        <!-- Download PDF Button -->
                        <button id="exportBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span class="text-base">Expor PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Summary Section -->
                @if(isset($summary) && !isset($error))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm text-gray-600 mb-1">Total Aset</h3>
                        <p class="text-lg font-bold text-[#213268]">{{ $summary['total_items'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm text-gray-600 mb-1">Total Biaya Perolehan</h3>
                        <p class="text-lg font-bold text-[#213268]">Rp {{ number_format($summary['total_acquisition_cost'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm text-gray-600 mb-1">Total Nilai Buku</h3>
                        <p class="text-lg font-bold text-[#213268]">Rp {{ number_format($summary['total_book_value'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm text-gray-600 mb-1">Total Penyusutan</h3>
                        <p class="text-lg font-bold text-[#213268]">Rp {{ number_format($summary['total_depreciation'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif

                <!-- Show error message if any -->
                @if(isset($error))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Kesalahan</p>
                    <p>{{ $error }}</p>
                </div>
                @endif

                <!-- Depreciation Report Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                    <a href="{{ route('report.depreciation', [
                                        'search' => $search ?? '',
                                        'sort' => ($sort == 'asset_name_asc') ? 'asset_name_desc' : 'asset_name_asc',
                                        'as_of_date' => $as_of_date ?? '',
                                        'asset_type' => $asset_type ?? ''
                                    ]) }}" class="text-white hover:text-gray-200">
                                        Nama Aset
                                        @if($sort == 'asset_name_asc') ↑ @elseif($sort == 'asset_name_desc') ↓ @endif
                                    </a>
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                    <a href="{{ route('report.depreciation', [
                                        'search' => $search ?? '',
                                        'sort' => ($sort == 'date_acquired_asc') ? 'date_acquired_desc' : 'date_acquired_asc',
                                        'as_of_date' => $as_of_date ?? '',
                                        'asset_type' => $asset_type ?? ''
                                    ]) }}" class="text-white hover:text-gray-200">
                                        Tanggal Perolehan
                                        @if($sort == 'date_acquired_asc') ↑ @elseif($sort == 'date_acquired_desc') ↓ @endif
                                    </a>
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-right">Biaya Pembelian</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-right">Nilai Sisa</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Masa Pakai (Bulan)</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Metode Penyusutan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Bulan dan Tahun</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-right">
                                    <a href="{{ route('report.depreciation', [
                                        'search' => $search ?? '',
                                        'sort' => ($sort == 'book_value_asc') ? 'book_value_desc' : 'book_value_asc',
                                        'as_of_date' => $as_of_date ?? '',
                                        'asset_type' => $asset_type ?? ''
                                    ]) }}" class="text-white hover:text-gray-200">
                                        Nilai Buku
                                        @if($sort == 'book_value_asc') ↑ @elseif($sort == 'book_value_desc') ↓ @endif
                                    </a>
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Gedung</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Ruangan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Subkategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($items) && count($items) > 0)
                                @foreach($items as $item)
                                <tr class="depreciation-row"
                                    data-purchase="{{ $item['purchase_cost'] }}"
                                    data-salvage="{{ $item['salvage_value'] }}"
                                    data-book="{{ $item['book_value_at_month_end'] }}">
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['asset_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ \Carbon\Carbon::parse($item['date_acquired'])->locale('id')->isoFormat('DD MMMM YYYY') }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-right purchase-cost">Rp {{ number_format($item['purchase_cost'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-right salvage-value">Rp {{ number_format($item['salvage_value'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">{{ $item['asset_life_months'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['depreciation_method'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @php
                                            try {
                                                // Try to parse and format the month_and_year
                                                echo \Carbon\Carbon::createFromFormat('F Y', $item['month_and_year'])->locale('id')->isoFormat('MMMM YYYY');
                                            } catch (\Exception $e) {
                                                // If parsing fails, show original value
                                                echo $item['month_and_year'];
                                            }
                                        @endphp
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-right book-value">Rp {{ number_format($item['book_value_at_month_end'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['building'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['room'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ ucfirst($item['asset_type']) }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['subcategory'] }}</td>
                                </tr>
                                @endforeach
                            @elseif(!isset($error))
                                <tr>
                                    <td colspan="13" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada data penyusutan ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($pagination) && $pagination && $pagination['total_pages'] > 1)
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ $pagination['current_page'] > 1 ? route('report.depreciation', [
                            'page' => $pagination['current_page'] - 1,
                            'limit' => $pagination['per_page'],
                            'search' => $search ?? '',
                            'sort' => $sort ?? '',
                            'as_of_date' => $as_of_date ?? '',
                            'asset_type' => $asset_type ?? ''
                        ]) : '#' }}"
                           class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ $pagination['current_page'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>

                        <div class="flex">
                            @php
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ route('report.depreciation', [
                                    'page' => 1,
                                    'limit' => $pagination['per_page'],
                                    'search' => $search ?? '',
                                    'sort' => $sort ?? '',
                                    'as_of_date' => $as_of_date ?? '',
                                    'asset_type' => $asset_type ?? ''
                                ]) }}" class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">1</a>

                                @if($start > 2)
                                    <span class="mx-1 flex items-center">...</span>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                <a href="{{ route('report.depreciation', [
                                    'page' => $i,
                                    'limit' => $pagination['per_page'],
                                    'search' => $search ?? '',
                                    'sort' => $sort ?? '',
                                    'as_of_date' => $as_of_date ?? '',
                                    'asset_type' => $asset_type ?? ''
                                ]) }}"
                                   class="w-8 h-8 flex items-center justify-center {{ $i == $pagination['current_page'] ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268]' }} rounded-md mx-1 text-sm">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if($end < $pagination['total_pages'])
                                @if($end < $pagination['total_pages'] - 1)
                            <span class="mx-1 flex items-center">...</span>
                                @endif

                                <a href="{{ route('report.depreciation', [
                                    'page' => $pagination['total_pages'],
                                    'limit' => $pagination['per_page'],
                                    'search' => $search ?? '',
                                    'sort' => $sort ?? '',
                                    'as_of_date' => $as_of_date ?? '',
                                    'asset_type' => $asset_type ?? ''
                                ]) }}" class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">
                                    {{ $pagination['total_pages'] }}
                                </a>
                            @endif
                        </div>

                        <a href="{{ $pagination['current_page'] < $pagination['total_pages'] ? route('report.depreciation', [
                            'page' => $pagination['current_page'] + 1,
                            'limit' => $pagination['per_page'],
                            'search' => $search ?? '',
                            'sort' => $sort ?? '',
                            'as_of_date' => $as_of_date ?? '',
                            'asset_type' => $asset_type ?? ''
                        ]) : '#' }}"
                           class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ $pagination['current_page'] >= $pagination['total_pages'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="relative mt-4 md:mt-0">
                        <select name="limit" form="depreciationFilterForm" onchange="document.getElementById('depreciationFilterForm').submit()" class="border border-[#D8DAE5] rounded-md py-1 px-3 pr-8 appearance-none text-[#213268] text-sm">
                            <option value="10" {{ ($pagination['per_page'] ?? 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ ($pagination['per_page'] ?? 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ ($pagination['per_page'] ?? 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ ($pagination['per_page'] ?? 10) == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                            </svg>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default date if empty
        const asOfDateInput = document.querySelector('input[name="as_of_date"]');
        if (asOfDateInput && !asOfDateInput.value) {
            asOfDateInput.value = new Date().toISOString().split('T')[0];
        }

        // Percentage toggle functionality
        const percentageToggle = document.getElementById('percentageToggle');
        if (percentageToggle) {
            percentageToggle.addEventListener('change', function() {
                const isPercentage = this.checked;
                updateDisplayValues(isPercentage);
            });

            // Initialize with percentage view (since toggle is checked by default)
            updateDisplayValues(true);
        }

        function updateDisplayValues(isPercentage) {
            const rows = document.querySelectorAll('.depreciation-row');

            rows.forEach(row => {
                const purchaseCost = parseFloat(row.getAttribute('data-purchase')) || 0;
                const salvageValue = parseFloat(row.getAttribute('data-salvage')) || 0;
                const bookValue = parseFloat(row.getAttribute('data-book')) || 0;

                // Get the cells to update
                const purchaseCell = row.querySelector('.purchase-cost');
                const salvageCell = row.querySelector('.salvage-value');
                const bookCell = row.querySelector('.book-value');

                if (isPercentage) {
                    // Update to percentage view
                    purchaseCell.textContent = '100%';
                    salvageCell.textContent = formatPercentage(salvageValue, purchaseCost);
                    bookCell.textContent = formatPercentage(bookValue, purchaseCost);
                } else {
                    // Update to currency view
                    purchaseCell.textContent = formatCurrency(purchaseCost);
                    salvageCell.textContent = formatCurrency(salvageValue);
                    bookCell.textContent = formatCurrency(bookValue);
                }
            });

            // Also update summary section
            updateSummarySection(isPercentage);
        }

        function updateSummarySection(isPercentage) {
            // Update the summary cards if they exist
            if (document.querySelector('.bg-blue-50')) {
                const summaryItems = document.querySelectorAll('.bg-blue-50 .text-lg');

                if (summaryItems && summaryItems.length >= 4) {
                    // Skip the first one (total number of assets)

                    // Store the original values if they don't exist yet
                    if (!window.originalSummaryValues) {
                        window.originalSummaryValues = {
                            acquisition: extractNumericValue(summaryItems[1].textContent),
                            bookValue: extractNumericValue(summaryItems[2].textContent),
                            depreciation: extractNumericValue(summaryItems[3].textContent)
                        };
                    }

                    const totalAcquisition = window.originalSummaryValues.acquisition;
                    const totalBookValue = window.originalSummaryValues.bookValue;
                    const totalDepreciation = window.originalSummaryValues.depreciation;

                    if (isPercentage) {
                        // Convert to percentages
                        summaryItems[1].textContent = '100%';
                        summaryItems[2].textContent = formatPercentage(totalBookValue, totalAcquisition);
                        summaryItems[3].textContent = formatPercentage(totalDepreciation, totalAcquisition);
                    } else {
                        // Restore currency format
                        summaryItems[1].textContent = formatCurrency(totalAcquisition);
                        summaryItems[2].textContent = formatCurrency(totalBookValue);
                        summaryItems[3].textContent = formatCurrency(totalDepreciation);
                    }
                }
            }
        }

        function formatPercentage(value, total) {
            if (!total || total === 0) return '0%';
            return (value / total * 100).toFixed(2) + '%';
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        function extractNumericValue(text) {
            if (!text) return 0;
            // Remove all non-numeric characters except decimal point
            const numStr = text.replace(/[^0-9]/g, '');
            return parseInt(numStr, 10) || 0;
        }

        // Initialize asset type and subcategory relationship
        const assetTypeSelect = document.querySelector('select[name="asset_type"]');
        const subcategorySearch = document.getElementById('subcategory_search');
        const subcategoryDropdown = document.getElementById('subcategory_dropdown');
        const subcategoryList = document.getElementById('subcategory_list');
        const selectedSubcategory = document.getElementById('selected_subcategory');

        if (assetTypeSelect && subcategorySearch) {
            // Set initial search value if exists in URL
            const urlParams = new URLSearchParams(window.location.search);
            const subcategoryValue = urlParams.get('subcategory');
            if (subcategoryValue) {
                subcategorySearch.value = subcategoryValue;
                selectedSubcategory.value = subcategoryValue;
                subcategorySearch.disabled = false;
                subcategorySearch.placeholder = 'Search subcategories';
            }

            // Load subcategories when asset type changes
            assetTypeSelect.addEventListener('change', function() {
                if (this.value) {
                    subcategorySearch.disabled = false;
                    subcategorySearch.placeholder = 'Cari subkategori';
                    subcategorySearch.value = '';
                    selectedSubcategory.value = '';

                    // Pre-load subcategories when asset type is selected
                    loadSubcategories('', this.value);
                } else {
                    subcategorySearch.disabled = true;
                    subcategorySearch.placeholder = 'Pilih tipe aset terlebih dahulu';
                    subcategorySearch.value = '';
                    selectedSubcategory.value = '';
                }
            });

            // Toggle dropdown visibility
            subcategorySearch.addEventListener('focus', function() {
                // Only show dropdown if an asset type is selected
                if (!assetTypeSelect.value) {
                    alert('Silakan pilih tipe aset terlebih dahulu');
                    return;
                }

                subcategoryDropdown.classList.remove('hidden');
                if (subcategoryList.children.length === 0) {
                    loadSubcategories('', assetTypeSelect.value); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!subcategorySearch.contains(e.target) && !subcategoryDropdown.contains(e.target)) {
                    subcategoryDropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                if (assetTypeSelect.value) {
                    loadSubcategories(e.target.value, assetTypeSelect.value);
                }
            }, 300);

            subcategorySearch.addEventListener('input', debouncedSearch);

            // Function to load subcategories based on asset type
            async function loadSubcategories(searchTerm, assetType) {
                if (!assetType) {
                    return;
                }

                console.log('Loading subcategories for:', assetType);

                // Clear previous content and show loading state
                subcategoryList.innerHTML = '';

                                    // Create loading indicator directly in the list
                    const loadingItem = document.createElement('li');
                    loadingItem.className = 'flex justify-center py-2 items-center';
                    loadingItem.innerHTML = `
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]"></div>
                        <span class="ml-2 text-gray-600">Memuat subkategori...</span>
                    `;
                    subcategoryList.appendChild(loadingItem);

                try {
                    // Use the direct API endpoint for subcategories with asset type filter
                    const response = await fetch(`{{ route('categories.by-asset-type') }}?asset_type=${encodeURIComponent(assetType)}${searchTerm ? '&search=' + encodeURIComponent(searchTerm) : ''}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin' // Include cookies for authentication
                    });

                    if (!response.ok) {
                        throw new Error(`Failed to fetch subcategories: ${response.status}`);
                    }

                    // Try to parse JSON, but handle HTML responses gracefully
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned non-JSON response');
                    }

                    const result = await response.json();
                    const subcategories = result.data || [];

                    console.log('Received subcategories:', subcategories.length);

                    // Clear the list including the loading indicator
                    subcategoryList.innerHTML = '';

                    if (subcategories.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada subkategori ditemukan';
                        subcategoryList.appendChild(noResults);
                    } else {
                        // First find exact matches (starts with search term)
                        let hasExactMatches = false;
                        if (searchTerm) {
                            subcategories.forEach(item => {
                                const subcategoryName = item.subcategory_name || 'Tidak Diketahui';
                                if (subcategoryName.toLowerCase().startsWith(searchTerm.toLowerCase())) {
                                    addSubcategoryOption(item, subcategoryName);
                                    hasExactMatches = true;
                                }
                            });
                        }

                        // If no exact matches or no search term, show all matches or contains matches
                        if (!hasExactMatches) {
                            subcategories.forEach(item => {
                                const subcategoryName = item.subcategory_name || 'Tidak Diketahui';
                                if (!searchTerm || subcategoryName.toLowerCase().includes(searchTerm.toLowerCase())) {
                                    addSubcategoryOption(item, subcategoryName);
                                }
                            });
                        }

                        // Add result count if there are many results
                        if (subcategories.length > 10) {
                                                            const countItem = document.createElement('li');
                                countItem.className = 'px-4 py-2 text-xs text-center text-gray-500 border-t';
                                countItem.textContent = `Menampilkan ${Math.min(subcategories.length, 50)} dari ${subcategories.length} subkategori`;
                                subcategoryList.appendChild(countItem);
                        }
                    }
                } catch (error) {
                    console.error('Error loading subcategories:', error);

                    // Clear the list including the loading indicator
                    subcategoryList.innerHTML = '';

                                    const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = `Gagal memuat subkategori: ${error.message}`;
                subcategoryList.appendChild(errorItem);

                    // Add manual entry option if user typed something
                    if (searchTerm) {
                        const manualOption = document.createElement('li');
                        manualOption.className = 'px-4 py-2 text-center bg-green-50 text-green-700 cursor-pointer hover:bg-green-100';
                        manualOption.textContent = `➕ Gunakan "${searchTerm}" sebagai subkategori`;
                        manualOption.addEventListener('click', function() {
                            selectedSubcategory.value = searchTerm;
                            subcategorySearch.value = searchTerm;
                            subcategoryDropdown.classList.add('hidden');
                        });
                        subcategoryList.appendChild(manualOption);
                    }

                    // Add more detailed diagnostic information
                    console.log('Request details:', {
                        url: `{{ route('categories.by-asset-type') }}?asset_type=${assetType}${searchTerm ? '&search=' + searchTerm : ''}`,
                        assetType,
                        searchTerm
                    });

                    // Also update the input placeholder to indicate the error
                    subcategorySearch.placeholder = "Gagal memuat subkategori";
                }
            }

            // Helper function to add subcategory option to the list
            function addSubcategoryOption(item, displayName) {
                const li = document.createElement('li');
                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                li.textContent = displayName;
                li.setAttribute('data-id', item.subcategory_id);
                li.setAttribute('data-name', displayName);

                li.addEventListener('click', function() {
                    // Set the selected subcategory data
                    const displayText = this.getAttribute('data-name');

                    // Store the subcategory name for search
                    selectedSubcategory.value = displayText;
                    subcategorySearch.value = displayText;

                    // Hide dropdown
                    subcategoryDropdown.classList.add('hidden');
                });

                subcategoryList.appendChild(li);
            }
        }

        // Debounce utility function to limit how often a function can be called
        function debounce(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        // Initialize building search functionality
        function initBuildingSearch() {
            const searchInput = document.getElementById('building_search');
            const dropdown = document.getElementById('building_dropdown');
            const buildingList = document.getElementById('building_list');
            const loadingIndicator = document.getElementById('building_loading');
            const selectedBuildingId = document.getElementById('selected_building_id');

            if (!searchInput || !dropdown || !buildingList) return;

            // Set initial search value if exists in URL
            const urlParams = new URLSearchParams(window.location.search);
            const buildingValue = urlParams.get('building');
            if (buildingValue) {
                searchInput.value = buildingValue;
                selectedBuildingId.value = buildingValue;
            }

            // Set a default error handler to show in the dropdown
            const showError = (message) => {
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = message || 'Gagal memuat gedung';
                buildingList.innerHTML = '';
                buildingList.appendChild(errorItem);

                // Also update the input placeholder to indicate the error
                searchInput.placeholder = "Gagal memuat gedung";
            };

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                dropdown.classList.remove('hidden');
                if (buildingList.children.length === 0) {
                    loadBuildings(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                loadBuildings(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load buildings
            async function loadBuildings(searchTerm) {
                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                buildingList.innerHTML = '';

                try {
                    // Use the API endpoint with proper headers to ensure JSON response
                    const response = await fetch(`{{ route('buildings.data') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch buildings');
                    }

                    // Try to parse JSON, but handle HTML responses gracefully
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON');
                    }

                    const result = await response.json();
                    let buildings = result.data || [];

                    // Populate dropdown
                    buildingList.innerHTML = '';

                    if (buildings.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada gedung ditemukan';
                        buildingList.appendChild(noResults);
                    } else {
                        buildings.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const buildingName = item.building_name || 'Tidak Diketahui';

                            li.textContent = buildingName;
                            li.setAttribute('data-id', item.building_id);
                            li.setAttribute('data-name', buildingName);

                            li.addEventListener('click', function() {
                                // Set the selected building data
                                const displayText = this.getAttribute('data-name');
                                const buildingId = this.getAttribute('data-id');

                                // Store the building ID and display name
                                selectedBuildingId.value = buildingId;
                                searchInput.value = displayText;

                                // Hide dropdown
                                dropdown.classList.add('hidden');

                                // Enable room search and reset it
                                const roomSearchInput = document.getElementById('room_search');
                                const selectedRoomId = document.getElementById('selected_room_id');
                                if (roomSearchInput) {
                                    roomSearchInput.disabled = false;
                                    roomSearchInput.placeholder = 'Cari ruangan';
                                    roomSearchInput.value = '';
                                }
                                if (selectedRoomId) {
                                    selectedRoomId.value = '';
                                }
                            });

                            buildingList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading buildings:', error);
                    showError(error.message);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize room search functionality
        function initRoomSearch() {
            const searchInput = document.getElementById('room_search');
            const dropdown = document.getElementById('room_dropdown');
            const roomList = document.getElementById('room_list');
            const loadingIndicator = document.getElementById('room_loading');
            const selectedRoomId = document.getElementById('selected_room_id');
            const selectedBuildingId = document.getElementById('selected_building_id');

            if (!searchInput || !dropdown || !roomList) return;

            // Set initial search value if exists in URL
            const urlParams = new URLSearchParams(window.location.search);
            const roomValue = urlParams.get('room');
            if (roomValue) {
                searchInput.value = roomValue;
                selectedRoomId.value = roomValue;
                // If room is set, we should enable the input
                searchInput.disabled = false;
                searchInput.placeholder = 'Search rooms';
            }

            // Set a default error handler to show in the dropdown
            const showError = (message) => {
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = message || 'Gagal memuat ruangan';
                roomList.innerHTML = '';
                roomList.appendChild(errorItem);

                // Also update the input placeholder to indicate the error
                searchInput.placeholder = "Gagal memuat ruangan";
            };

            // Add a fallback option to manually enter a room name if API fails
            const addFallbackOption = () => {
                const fallbackOption = document.createElement('li');
                fallbackOption.className = 'px-4 py-2 bg-blue-50 text-blue-700 cursor-pointer';
                fallbackOption.textContent = '➕ Gunakan input saat ini sebagai nama ruangan';

                fallbackOption.addEventListener('click', function() {
                    const inputValue = searchInput.value.trim();
                    if (inputValue) {
                        selectedRoomId.value = inputValue;
                        dropdown.classList.add('hidden');
                    }
                });

                roomList.appendChild(fallbackOption);
            };

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                // Only show dropdown if a building is selected
                if (!selectedBuildingId.value) {
                    alert('Silakan pilih gedung terlebih dahulu');
                    return;
                }

                dropdown.classList.remove('hidden');
                if (roomList.children.length === 0) {
                    loadRooms('', selectedBuildingId.value); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                if (selectedBuildingId.value) {
                    loadRooms(e.target.value, selectedBuildingId.value);
                }
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load rooms filtered by building
            async function loadRooms(searchTerm, buildingId) {
                if (!buildingId) {
                    return;
                }

                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                try {
                    // Use the API endpoint with proper headers to ensure JSON response
                    const response = await fetch(`{{ route('rooms.data') }}?building_id=${buildingId}${searchTerm ? '&search=' + encodeURIComponent(searchTerm) : ''}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch rooms');
                    }

                    // Try to parse JSON, but handle HTML responses gracefully
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON');
                    }

                    const result = await response.json();
                    let rooms = result.data || [];

                    // Populate dropdown
                    roomList.innerHTML = '';

                    if (rooms.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada ruangan ditemukan untuk gedung ini';
                        roomList.appendChild(noResults);

                        // Add fallback option to use typed text
                        if (searchTerm) {
                            addFallbackOption();
                        }
                    } else {
                        rooms.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const roomName = item.room_name || 'Tidak Diketahui';

                            li.textContent = roomName;
                            li.setAttribute('data-id', item.room_id);
                            li.setAttribute('data-name', roomName);

                            li.addEventListener('click', function() {
                                // Set the selected room data
                                const displayText = this.getAttribute('data-name');
                                const roomId = this.getAttribute('data-id');

                                // Store the room ID and display name
                                selectedRoomId.value = roomId;
                                searchInput.value = displayText;

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            roomList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading rooms:', error);
                    showError(error.message);

                    // Add fallback option when there's an API error
                    if (searchTerm) {
                        addFallbackOption();
                    }
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize asset master search functionality
        function initAssetMasterSearch() {
            const searchInput = document.getElementById('asset_master_search');
            const dropdown = document.getElementById('asset_master_dropdown');
            const assetList = document.getElementById('asset_master_list');
            const loadingIndicator = document.getElementById('asset_master_loading');
            const selectedAssetId = document.getElementById('selected_asset_master_id');

            if (!searchInput || !dropdown || !assetList) return;

            // Set initial search value if exists in URL
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search');
            if (searchValue) {
                searchInput.value = searchValue;
                selectedAssetId.value = searchValue;
            }

            // Set a default error handler to show in the dropdown
            const showError = (message) => {
                const errorItem = document.createElement('li');
                errorItem.className = 'px-4 py-2 text-red-500';
                errorItem.textContent = message || 'Gagal memuat master aset';
                assetList.innerHTML = '';
                assetList.appendChild(errorItem);

                // Also update the input placeholder to indicate the error
                searchInput.placeholder = "Gagal memuat master aset";
            };

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                dropdown.classList.remove('hidden');
                if (assetList.children.length === 0) {
                    loadAssetMasters(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                loadAssetMasters(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load asset masters
            async function loadAssetMasters(searchTerm) {
                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                assetList.innerHTML = '';

                try {
                    // Fetch asset masters data from the API with proper headers
                    const response = await fetch(`{{ route('asset-master.data') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch asset masters');
                    }

                    // Try to parse JSON, but handle HTML responses gracefully
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned HTML instead of JSON');
                    }

                    const result = await response.json();
                    let assetMasters = result.masterAssets || [];

                    // Populate dropdown
                    assetList.innerHTML = '';

                    if (assetMasters.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'Tidak ada master aset ditemukan';
                        assetList.appendChild(noResults);
                    } else {
                        assetMasters.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const assetMasterName = item.asset_name || 'Unknown';
                            const assetMasterCode = item.asset_master_code || '';

                            // Only display the asset name in the dropdown
                            li.textContent = assetMasterName;
                            li.setAttribute('data-id', item.asset_master_id);
                            li.setAttribute('data-name', assetMasterName);
                            li.setAttribute('data-code', assetMasterCode);

                            li.addEventListener('click', function() {
                                // Set the selected asset master data
                                // Only use the asset name for display
                                const displayText = this.getAttribute('data-name');

                                // Store just the name in the hidden field for search
                                selectedAssetId.value = this.getAttribute('data-name');

                                // Update the search input with the full display text for better UX
                                searchInput.value = displayText;

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            assetList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading asset masters:', error);
                    showError(error.message);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize all the search components
        initBuildingSearch();
        initRoomSearch();
        initAssetMasterSearch();

        // Form submission handler
        const depreciationFilterForm = document.getElementById('depreciationFilterForm');
        if (depreciationFilterForm) {
            depreciationFilterForm.addEventListener('submit', function(e) {
                // Make sure the search value is set from the asset search dropdown
                const searchInput = document.getElementById('asset_master_search');
                const selectedAssetId = document.getElementById('selected_asset_master_id');

                // If the user typed something but didn't select from dropdown,
                // use the input value for search
                if (searchInput.value && !selectedAssetId.value) {
                    selectedAssetId.value = searchInput.value;
                }
            });
        }

        // Export PDF functionality
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                // Build the export URL with all current parameters
                const exportUrl = "{{ route('report.depreciation.export-pdf') }}?" + new URLSearchParams({
                    search: "{{ $search ?? '' }}",
                    sort: "{{ $sort ?? 'asset_name_asc' }}",
                    as_of_date: "{{ $as_of_date ?? now()->format('Y-m-d') }}",
                    asset_type: "{{ $asset_type ?? '' }}",
                    subcategory: document.getElementById('selected_subcategory')?.value || "",
                    building: document.getElementById('selected_building_id')?.value || "",
                    room: document.getElementById('selected_room_id')?.value || ""
                }).toString();

                // Open in a new window
                window.open(exportUrl, '_blank');
            });
        }
    });
</script>
@endpush
@endsection
