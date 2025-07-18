@extends('Layout.app')

@section('title', 'Laporan Penyusutan')

@section('content')
    @include('Layout.loading')
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
                    <form action="{{ route('report.depreciation') }}" method="GET" id="depreciationFilterForm"
                        data-no-loading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                                <div class="relative">
                                    <input type="text" id="building_search"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                        placeholder="Cari Gedung" autocomplete="off">
                                    <input type="hidden" name="building" id="selected_building_id">

                                    <!-- Building Dropdown -->
                                    <div id="building_dropdown"
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                        <!-- Loading indicator -->
                                        <div id="building_loading" class="flex justify-center py-2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]">
                                            </div>
                                            <span class="ml-2 text-gray-600">Memuat Gedung...</span>
                                        </div>
                                        <ul id="building_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                                <div class="relative">
                                    <input type="text" id="room_search"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                        placeholder="Pilih Gedung Telebih Dahulu" autocomplete="off" disabled>
                                    <input type="hidden" name="room" id="selected_room_id">

                                    <!-- Room Dropdown -->
                                    <div id="room_dropdown"
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                        <!-- Loading indicator -->
                                        <div id="room_loading" class="flex justify-center py-2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]">
                                            </div>
                                            <span class="ml-2 text-gray-600">Memuat Ruangan...</span>
                                        </div>
                                        <ul id="room_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                                <div class="relative">
                                    <select name="asset_type"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                        <option value="">Semua Tipe Aset</option>
                                        <option value="medical" {{ $asset_type == 'medical' ? 'selected' : '' }}>Medis
                                        </option>
                                        <option value="non_medical" {{ $asset_type == 'non_medical' ? 'selected' : '' }}>Non
                                            Medis</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <div class="relative">
                                    <input type="text" id="subcategory_search"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                        placeholder="Pilih Tipe Aset Terlebih Dahulu" autocomplete="off" disabled>
                                    <input type="hidden" name="subcategory" id="selected_subcategory">

                                    <!-- Subcategory Dropdown -->
                                    <div id="subcategory_dropdown"
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                        <ul id="subcategory_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berdasarkan Tanggal</label>
                                <div class="relative">
                                    <input type="month" name="as_of_date"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268]"
                                        value="{{ $as_of_date ?? '' }}" placeholder="YYYY-MM">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Master Aset <span
                                        class="text-xs text-gray-500">(hanya aset yang dapat disusutkan)</span></label>
                                <div class="relative">
                                    <input type="text" id="asset_master_search"
                                        class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268] focus:outline-none focus:border-[#213268] focus:ring focus:ring-[#213268] focus:ring-opacity-20"
                                        placeholder="Cari aset yang dapat disusutkan" autocomplete="off">
                                    <input type="hidden" name="search" id="selected_asset_master_id">

                                    <!-- Dropdown -->
                                    <div id="asset_master_dropdown"
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                        <!-- Loading indicator -->
                                        <div id="asset_master_loading" class="flex justify-center py-2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]">
                                            </div>
                                            <span class="ml-2 text-gray-600">Mencari Master Aset...</span>
                                        </div>
                                        <ul id="asset_master_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Button -->
                        <div class="mt-4">
                            <button type="submit"
                                class="w-full bg-[#213268] text-white py-3 rounded-md hover:bg-[#1a275a] transition-colors">
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
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#213268] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]">
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">Persen</span>
                                </label>
                            </div>

                            <!-- Download PDF Button -->
                            <button id="exportBtn"
                                class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
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
                                <p class="text-lg font-bold text-[#213268]">Rp
                                    {{ number_format($summary['total_acquisition_cost'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-sm text-gray-600 mb-1">Total Nilai Buku</h3>
                                <p class="text-lg font-bold text-[#213268]">Rp
                                    {{ number_format($summary['total_book_value'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-sm text-gray-600 mb-1">Total Penyusutan</h3>
                                <p class="text-lg font-bold text-[#213268]">Rp
                                    {{ number_format($summary['total_depreciation'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
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
                                            @if($sort == 'date_acquired_asc') ↑ @elseif($sort == 'date_acquired_desc') ↓
                                            @endif
                                        </a>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-right">Biaya Pembelian
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-right">Nilai Sisa</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Masa Pakai (Bulan)
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Metode Penyusutan
                                    </th>
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($items) && count($items) > 0)
                                    @foreach($items as $item)
                                        <tr class="depreciation-row" data-purchase="{{ $item['purchase_cost'] }}"
                                            data-salvage="{{ $item['salvage_value'] }}"
                                            data-book="{{ $item['book_value_at_month_end'] }}">
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['asset_name'] }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                {{ \Carbon\Carbon::parse($item['date_acquired'])->locale('id')->isoFormat('DD MMMM YYYY') }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-right purchase-cost">Rp
                                                {{ number_format($item['purchase_cost'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-right salvage-value">Rp
                                                {{ number_format($item['salvage_value'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                {{ $item['asset_life_months'] }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['depreciation_method'] }}
                                            </td>
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
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-right book-value">Rp
                                                {{ number_format($item['book_value_at_month_end'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['building'] }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['room'] }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                @if($item['asset_type'] == 'medical')
                                                    Medis
                                                @elseif($item['asset_type'] == 'non_medical')
                                                    Non Medis
                                                @else
                                                    {{ ucfirst($item['asset_type']) }}
                                                @endif
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $item['subcategory'] }}</td>
                                        </tr>
                                    @endforeach
                                @elseif(!isset($error))
                                    <tr>
                                        <td colspan="13" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada
                                            data penyusutan ditemukan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                @if(isset($error))
                    showToast("{{ $error }}", 'error');
                @endif

                function showToast(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.id = type + 'Notification' + Date.now();
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

                    const hasHTML = /<[a-z][\s\S]*>/i.test(message);

                    if (type === 'success') {
                        notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                        notification.innerHTML = `
                                    <div class="flex items-start">
                                        <div class="py-1">
                                            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold">Berhasil!</p>
                                            <div>${message}</div>
                                        </div>
                                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                                    </div>
                                `;
                    } else {
                        notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                        const wrapper = document.createElement('div');
                        wrapper.className = 'flex items-start';
                        const iconContainer = document.createElement('div');
                        iconContainer.className = 'py-1 flex-shrink-0';
                        iconContainer.innerHTML = `
                                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                `;

                        const contentContainer = document.createElement('div');
                        contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';
                        const title = document.createElement('p');
                        title.className = 'font-bold';
                        title.textContent = 'Gagal!';
                        contentContainer.appendChild(title);
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'error-message';
                        if (hasHTML) {
                            messageContainer.innerHTML = message;
                        } else {
                            messageContainer.textContent = message;
                        }
                        contentContainer.appendChild(messageContainer);
                        const closeBtn = document.createElement('span');
                        closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                        closeBtn.textContent = '×';
                        closeBtn.onclick = function () {
                            notification.remove();
                        };
                        wrapper.appendChild(iconContainer);
                        wrapper.appendChild(contentContainer);
                        wrapper.appendChild(closeBtn);
                        notification.appendChild(wrapper);
                    }
                    document.body.appendChild(notification);
                    setTimeout(() => {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => notification.remove(), 500);
                    }, 5000);
                }

                document.head.insertAdjacentHTML('beforeend', `
                            <style>
                                @keyframes slideInRight {
                                    from { transform: translateX(100%); }
                                    to { transform: translateX(0); }
                                }
                                .animate-slide-in-right {
                                    animation: slideInRight 0.3s ease-out forwards;
                                }
                                .error-message ul {
                                    margin-top: 0.5rem;
                                    padding-left: 1.5rem;
                                }
                                .error-message ul li {
                                    margin-bottom: 0.25rem;
                                }
                                .error-message ul li:last-child {
                                    margin-bottom: 0;
                                }
                            </style>
                        `);

                const percentageToggle = document.getElementById('percentageToggle');
                if (percentageToggle) {
                    percentageToggle.addEventListener('change', function () {
                        const isPercentage = this.checked;
                        updateDisplayValues(isPercentage);
                    });

                    updateDisplayValues(true);
                }

                function updateDisplayValues(isPercentage) {
                    const rows = document.querySelectorAll('.depreciation-row');

                    rows.forEach(row => {
                        const purchaseCost = parseFloat(row.getAttribute('data-purchase')) || 0;
                        const salvageValue = parseFloat(row.getAttribute('data-salvage')) || 0;
                        const bookValue = parseFloat(row.getAttribute('data-book')) || 0;
                        const purchaseCell = row.querySelector('.purchase-cost');
                        const salvageCell = row.querySelector('.salvage-value');
                        const bookCell = row.querySelector('.book-value');

                        if (isPercentage) {
                            purchaseCell.textContent = '100%';
                            salvageCell.textContent = formatPercentage(salvageValue, purchaseCost);
                            bookCell.textContent = formatPercentage(bookValue, purchaseCost);
                        } else {
                            purchaseCell.textContent = formatCurrency(purchaseCost);
                            salvageCell.textContent = formatCurrency(salvageValue);
                            bookCell.textContent = formatCurrency(bookValue);
                        }
                    });

                    updateSummarySection(isPercentage);
                }

                function updateSummarySection(isPercentage) {
                    if (document.querySelector('.bg-blue-50')) {
                        const summaryItems = document.querySelectorAll('.bg-blue-50 .text-lg');

                        if (summaryItems && summaryItems.length >= 4) {
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
                                summaryItems[1].textContent = '100%';
                                summaryItems[2].textContent = formatPercentage(totalBookValue, totalAcquisition);
                                summaryItems[3].textContent = formatPercentage(totalDepreciation, totalAcquisition);
                            } else {
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
                    const numStr = text.replace(/[^0-9]/g, '');
                    return parseInt(numStr, 10) || 0;
                }

                const assetTypeSelect = document.querySelector('select[name="asset_type"]');
                const subcategorySearch = document.getElementById('subcategory_search');
                const subcategoryDropdown = document.getElementById('subcategory_dropdown');
                const subcategoryList = document.getElementById('subcategory_list');
                const selectedSubcategory = document.getElementById('selected_subcategory');

                if (assetTypeSelect && subcategorySearch) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const subcategoryValue = urlParams.get('subcategory');
                    if (subcategoryValue) {
                        subcategorySearch.value = subcategoryValue;
                        selectedSubcategory.value = subcategoryValue;
                        subcategorySearch.disabled = false;
                        subcategorySearch.placeholder = 'Cari Kategori';
                    }

                    assetTypeSelect.addEventListener('change', function () {
                        if (this.value) {
                            subcategorySearch.disabled = false;
                            subcategorySearch.placeholder = 'Cari Kategori';
                            subcategorySearch.value = '';
                            selectedSubcategory.value = '';

                            loadSubcategories('', this.value);

                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-blue-100 text-blue-800 px-4 py-2 rounded shadow-md z-50';
                            toast.textContent = 'Memuat kategori berdasarkan tipe aset...';
                            document.body.appendChild(toast);

                            setTimeout(() => {
                                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                                setTimeout(() => toast.remove(), 500);
                            }, 2000);
                        } else {
                            subcategorySearch.disabled = true;
                            subcategorySearch.placeholder = 'Pilih tipe aset terlebih dahulu';
                            subcategorySearch.value = '';
                            selectedSubcategory.value = '';
                        }
                    });

                    subcategorySearch.addEventListener('focus', function () {
                        if (!assetTypeSelect.value) {
                            alert('Silakan pilih tipe aset terlebih dahulu');
                            return;
                        }

                        subcategoryDropdown.classList.remove('hidden');
                        if (subcategoryList.children.length === 0) {
                            loadSubcategories('', assetTypeSelect.value);
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!subcategorySearch.contains(e.target) && !subcategoryDropdown.contains(e.target)) {
                            subcategoryDropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        if (assetTypeSelect.value) {
                            loadSubcategories(e.target.value, assetTypeSelect.value);
                        }
                    }, 300);

                    subcategorySearch.addEventListener('input', debouncedSearch);

                    if (assetTypeSelect.value) {
                        setTimeout(() => {
                            loadSubcategories('', assetTypeSelect.value);
                        }, 500);
                    }

                    async function loadSubcategories(searchTerm, assetType) {
                        if (!assetType) {
                            return;
                        }

                        subcategoryList.innerHTML = '';

                        const loadingItem = document.createElement('li');
                        loadingItem.className = 'flex justify-center py-2 items-center';
                        loadingItem.innerHTML = `
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]"></div>
                                        <span class="ml-2 text-gray-600">Memuat kategori...</span>
                                    `;
                        subcategoryList.appendChild(loadingItem);

                        try {
                            const controller = new AbortController();
                            const timeoutId = setTimeout(() => controller.abort(), 10000);
                            const apiUrl = `{{ route('categories') }}?asset_type=${encodeURIComponent(assetType)}&search=${encodeURIComponent(searchTerm || '')}&json=true&pagination=false`;
                            const response = await fetch(apiUrl, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Cache-Control': 'no-cache'
                                },
                                credentials: 'same-origin',
                                signal: controller.signal
                            }).finally(() => clearTimeout(timeoutId));

                            if (!response.ok) {
                                throw new Error(`Failed to fetch categories: ${response.status}`);
                            }

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Server returned non-JSON response');
                            }

                            let result;
                            try {
                                result = await response.json();
                            } catch (jsonError) {
                                console.error('JSON parse error:', jsonError);
                                throw new Error('Server returned invalid JSON');
                            }

                            let subcategories = [];
                            if (Array.isArray(result)) {
                                subcategories = result;
                            } else if (result.subcategories && Array.isArray(result.subcategories)) {
                                subcategories = result.subcategories;
                            } else if (result.data && Array.isArray(result.data)) {
                                subcategories = result.data;
                            }

                            subcategoryList.innerHTML = '';

                            if (subcategories.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Tidak ada kategori ditemukan';
                                subcategoryList.appendChild(noResults);
                            } else {
                                const searchHelpMsg = document.createElement('li');
                                searchHelpMsg.className = 'px-4 py-2 text-gray-500 italic text-center';
                                searchHelpMsg.textContent = 'Ketik untuk mencari...';
                                subcategoryList.appendChild(searchHelpMsg);

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

                                if (!hasExactMatches) {
                                    subcategories.forEach(item => {
                                        const subcategoryName = item.subcategory_name || 'Tidak Diketahui';
                                        if (!searchTerm || subcategoryName.toLowerCase().includes(searchTerm.toLowerCase())) {
                                            addSubcategoryOption(item, subcategoryName);
                                        }
                                    });
                                }

                                if (subcategories.length > 10) {
                                    const countItem = document.createElement('li');
                                    countItem.className = 'px-4 py-2 text-xs text-center text-gray-500 border-t';
                                    countItem.textContent = `Menampilkan ${Math.min(subcategories.length, 50)} dari ${subcategories.length} kategori`;
                                    subcategoryList.appendChild(countItem);
                                }
                            }
                        } catch (error) {
                            console.error('Error loading subcategories:', error);

                            subcategoryList.innerHTML = '';

                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';

                            if (error.name === 'AbortError') {
                                errorItem.textContent = 'Permintaan timeout. Server tidak merespon dalam waktu yang ditentukan.';
                            } else {
                                errorItem.textContent = `Gagal memuat kategori: ${error.message}`;
                            }
                            subcategoryList.appendChild(errorItem);

                            const retryOption = document.createElement('li');
                            retryOption.className = 'px-4 py-2 text-center bg-blue-50 text-blue-700 cursor-pointer hover:bg-blue-100';
                            retryOption.textContent = '🔄 Coba lagi';
                            retryOption.addEventListener('click', function () {
                                loadSubcategories(searchTerm, assetType);
                            });
                            subcategoryList.appendChild(retryOption);
                            subcategorySearch.placeholder = "Gagal memuat kategori";
                        }
                    }

                    function addSubcategoryOption(item, displayName) {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                        li.textContent = displayName;
                        li.setAttribute('data-id', item.subcategory_id);
                        li.setAttribute('data-name', displayName);
                        li.addEventListener('click', function () {
                            const displayText = this.getAttribute('data-name');
                            const subcategoryId = this.getAttribute('data-id');
                            selectedSubcategory.value = subcategoryId;
                            subcategorySearch.value = displayText;
                            subcategoryDropdown.classList.add('hidden');
                        });

                        subcategoryList.appendChild(li);
                    }
                }

                function debounce(func, wait, immediate) {
                    let timeout;
                    return function () {
                        const context = this, args = arguments;
                        const later = function () {
                            timeout = null;
                            if (!immediate) func.apply(context, args);
                        };
                        const callNow = immediate && !timeout;
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                        if (callNow) func.apply(context, args);
                    };
                }

                function initBuildingSearch() {
                    const searchInput = document.getElementById('building_search');
                    const dropdown = document.getElementById('building_dropdown');
                    const buildingList = document.getElementById('building_list');
                    const loadingIndicator = document.getElementById('building_loading');
                    const selectedBuildingId = document.getElementById('selected_building_id');

                    if (!searchInput || !dropdown || !buildingList) return;

                    const urlParams = new URLSearchParams(window.location.search);
                    const buildingValue = urlParams.get('building');
                    if (buildingValue) {
                        searchInput.value = buildingValue;
                        selectedBuildingId.value = buildingValue;
                    }

                    const showError = (message) => {
                        const errorItem = document.createElement('li');
                        errorItem.className = 'px-4 py-2 text-red-500';
                        errorItem.textContent = message || 'Gagal memuat gedung';
                        buildingList.innerHTML = '';
                        buildingList.appendChild(errorItem);
                        searchInput.placeholder = "Gagal memuat gedung";
                    };

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (buildingList.children.length === 0) {
                            loadBuildings('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        loadBuildings(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadBuildings(searchTerm) {
                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        buildingList.innerHTML = '';

                        try {
                            const response = await fetch(`{{ route('buildings') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Failed to fetch buildings');
                            }

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Server returned HTML instead of JSON');
                            }

                            const result = await response.json();
                            let buildings = result.data || [];

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

                                    li.addEventListener('click', function () {
                                        const displayText = this.getAttribute('data-name');
                                        const buildingId = this.getAttribute('data-id');

                                        selectedBuildingId.value = buildingId;
                                        searchInput.value = displayText;
                                        dropdown.classList.add('hidden');

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

                                        const roomLoadingIndicator = document.getElementById('room_loading');
                                        if (roomLoadingIndicator) {
                                            roomLoadingIndicator.classList.remove('hidden');
                                        }

                                        const roomList = document.getElementById('room_list');
                                        const roomDropdown = document.getElementById('room_dropdown');
                                        if (roomList && roomSearchInput && roomDropdown) {
                                            roomList.innerHTML = '';

                                            loadRooms('', buildingId);

                                            const toast = document.createElement('div');
                                            toast.className = 'fixed bottom-4 right-4 bg-blue-100 text-blue-800 px-4 py-2 rounded shadow-md z-50';
                                            toast.textContent = 'Memuat ruangan untuk gedung yang dipilih...';
                                            document.body.appendChild(toast);

                                            setTimeout(() => {
                                                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                                                setTimeout(() => toast.remove(), 500);
                                            }, 2000);
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

                function initRoomSearch() {
                    const searchInput = document.getElementById('room_search');
                    const dropdown = document.getElementById('room_dropdown');
                    const roomList = document.getElementById('room_list');
                    const loadingIndicator = document.getElementById('room_loading');
                    const selectedRoomId = document.getElementById('selected_room_id');
                    const selectedBuildingId = document.getElementById('selected_building_id');

                    if (!searchInput || !dropdown || !roomList) return;

                    const urlParams = new URLSearchParams(window.location.search);
                    const roomValue = urlParams.get('room');
                    if (roomValue) {
                        searchInput.value = roomValue;
                        selectedRoomId.value = roomValue;
                        searchInput.disabled = false;
                        searchInput.placeholder = 'Search rooms';
                    }

                    const showError = (message) => {
                        const errorItem = document.createElement('li');
                        errorItem.className = 'px-4 py-2 text-red-500';
                        errorItem.textContent = message || 'Gagal memuat ruangan';
                        roomList.innerHTML = '';
                        roomList.appendChild(errorItem);
                        searchInput.placeholder = "Gagal memuat ruangan";
                    };

                    searchInput.addEventListener('focus', function () {
                        if (!selectedBuildingId.value) {
                            alert('Silakan pilih gedung terlebih dahulu');
                            return;
                        }
                        dropdown.classList.remove('hidden');
                        if (roomList.children.length === 0) {
                            loadRooms('', selectedBuildingId.value);
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        if (selectedBuildingId.value) {
                            loadRooms(e.target.value, selectedBuildingId.value);
                        }
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadRooms(searchTerm, buildingId) {
                        if (!buildingId) {
                            searchInput.value = '';
                            searchInput.placeholder = 'Pilih gedung terlebih dahulu';
                            searchInput.disabled = true;
                            return;
                        }

                        searchInput.disabled = false;
                        searchInput.placeholder = "Cari ruangan...";

                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        roomList.innerHTML = '';

                        if (dropdown) dropdown.classList.remove('hidden');

                        try {
                            const apiUrl = `{{ route('rooms') }}?building_id=${encodeURIComponent(buildingId)}&search=${encodeURIComponent(searchTerm || '')}`;

                            const response = await fetch(apiUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Cache-Control': 'no-cache'
                                },
                                credentials: 'same-origin'
                            });

                            if (!response.ok) {
                                throw new Error(`Failed to fetch rooms: ${response.status} ${response.statusText}`);
                            }

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Server returned non-JSON response');
                            }

                            const result = await response.json();

                            let rooms = [];
                            if (Array.isArray(result)) {
                                rooms = result;
                            } else if (result.data && Array.isArray(result.data)) {
                                rooms = result.data;
                            } else if (result.rooms && Array.isArray(result.rooms)) {
                                rooms = result.rooms;
                            } else {
                                console.error('Unexpected API response format:', result);
                            }

                            roomList.innerHTML = '';

                            if (rooms.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Tidak ada ruangan ditemukan untuk gedung ini';
                                roomList.appendChild(noResults);
                            } else {
                                if (rooms.length > 5) {
                                    const searchHelpMsg = document.createElement('li');
                                    searchHelpMsg.className = 'px-4 py-2 text-gray-500 italic text-center';
                                    searchHelpMsg.textContent = 'Ketik untuk mencari...';
                                    roomList.appendChild(searchHelpMsg);
                                }

                                let exactMatches = 0;
                                if (searchTerm) {
                                    rooms.forEach(item => {
                                        const roomName = item.room_name || item.name || 'Tidak Diketahui';
                                        if (roomName.toLowerCase().startsWith(searchTerm.toLowerCase())) {
                                            addRoomOption(item, roomName);
                                            exactMatches++;
                                        }
                                    });
                                }

                                rooms.forEach(item => {
                                    const roomName = item.room_name || item.name || 'Tidak Diketahui';
                                    if (!searchTerm ||
                                        (!roomName.toLowerCase().startsWith(searchTerm.toLowerCase()) &&
                                            roomName.toLowerCase().includes(searchTerm.toLowerCase()))) {
                                        addRoomOption(item, roomName);
                                    }
                                });

                                if (rooms.length > 10) {
                                    const countItem = document.createElement('li');
                                    countItem.className = 'px-4 py-2 text-xs text-center text-gray-500 border-t';
                                    countItem.textContent = `Menampilkan ${Math.min(rooms.length, 50)} dari ${rooms.length} ruangan`;
                                    roomList.appendChild(countItem);
                                }
                            }
                        } catch (error) {
                            console.error('Error loading rooms:', error);
                            roomList.innerHTML = '';

                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';
                            errorItem.textContent = `Error: ${error.message}`;
                            roomList.appendChild(errorItem);

                            const retryOption = document.createElement('li');
                            retryOption.className = 'px-4 py-2 text-center bg-blue-50 text-blue-700 cursor-pointer hover:bg-blue-100';
                            retryOption.textContent = '🔄 Coba lagi';
                            retryOption.addEventListener('click', function () {
                                loadRooms(searchTerm, buildingId);
                            });
                            roomList.appendChild(retryOption);
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }

                    function addRoomOption(item, displayName) {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        const roomId = item.room_id || item.id || '';

                        if (!roomId) {
                            console.warn('Room missing required properties:', item);
                            return;
                        }

                        li.textContent = displayName;
                        li.setAttribute('data-id', roomId);
                        li.setAttribute('data-name', displayName);

                        li.addEventListener('click', function () {
                            selectedRoomId.value = this.getAttribute('data-id');
                            searchInput.value = this.getAttribute('data-name');

                            dropdown.classList.add('hidden');
                        });

                        roomList.appendChild(li);
                    }
                }

                function initAssetMasterSearch() {
                    const searchInput = document.getElementById('asset_master_search');
                    const dropdown = document.getElementById('asset_master_dropdown');
                    const assetList = document.getElementById('asset_master_list');
                    const loadingIndicator = document.getElementById('asset_master_loading');
                    const selectedAssetId = document.getElementById('selected_asset_master_id');

                    if (!searchInput || !dropdown || !assetList) return;

                    const urlParams = new URLSearchParams(window.location.search);
                    const searchValue = urlParams.get('search');
                    if (searchValue) {
                        searchInput.value = searchValue;
                        selectedAssetId.value = searchValue;
                    }

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (assetList.children.length === 0) {
                            loadAssetMasters('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        loadAssetMasters(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadAssetMasters(searchTerm) {
                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        assetList.innerHTML = '';

                        try {
                            const controller = new AbortController();
                            const timeoutId = setTimeout(() => controller.abort(), 10000);
                            const apiUrl = `{{ route('asset-master') }}?search=${encodeURIComponent(searchTerm || '')}&is_depreciable=true`;

                            const response = await fetch(apiUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Cache-Control': 'no-cache'
                                },
                                signal: controller.signal
                            }).finally(() => clearTimeout(timeoutId));

                            if (!response.ok) {
                                throw new Error(`Failed to fetch asset masters: ${response.status}`);
                            }

                            let result;
                            try {
                                result = await response.json();
                            } catch (jsonError) {
                                console.error('JSON parse error:', jsonError);
                                throw new Error('Server returned invalid JSON');
                            }

                            let assetMasters = result.masterAssets || [];

                            assetList.innerHTML = '';

                            if (assetMasters.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Tidak ada aset yang dapat disusutkan ditemukan';
                                assetList.appendChild(noResults);
                            } else {
                                if (assetMasters.length > 5) {
                                    const searchHelpMsg = document.createElement('li');
                                    searchHelpMsg.className = 'px-4 py-2 text-gray-500 italic text-center';
                                    searchHelpMsg.textContent = 'Ketik untuk mencari...';
                                    assetList.appendChild(searchHelpMsg);
                                }

                                let exactMatches = 0;
                                if (searchTerm) {
                                    assetMasters.forEach(item => {
                                        const assetMasterName = item.asset_name || 'Unknown';
                                        if (assetMasterName.toLowerCase().startsWith(searchTerm.toLowerCase())) {
                                            addAssetMasterOption(item);
                                            exactMatches++;
                                        }
                                    });
                                }
                                assetMasters.forEach(item => {
                                    const assetMasterName = item.asset_name || 'Unknown';
                                    if (!searchTerm ||
                                        (!assetMasterName.toLowerCase().startsWith(searchTerm.toLowerCase()) &&
                                            assetMasterName.toLowerCase().includes(searchTerm.toLowerCase()))) {
                                        addAssetMasterOption(item);
                                    }
                                });

                                if (assetMasters.length > 10) {
                                    const countItem = document.createElement('li');
                                    countItem.className = 'px-4 py-2 text-xs text-center text-gray-500 border-t';
                                    countItem.textContent = `Menampilkan ${Math.min(assetMasters.length, 50)} dari ${assetMasters.length} aset`;
                                    assetList.appendChild(countItem);
                                }
                            }
                        } catch (error) {
                            console.error('Error loading asset masters:', error);
                            assetList.innerHTML = '';
                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';
                            if (error.name === 'AbortError') {
                                errorItem.textContent = 'Permintaan timeout. Server tidak merespon dalam waktu yang ditentukan.';
                            } else {
                                errorItem.textContent = `Gagal memuat aset yang dapat disusutkan: ${error.message}`;
                            }
                            assetList.appendChild(errorItem);
                            const retryOption = document.createElement('li');
                            retryOption.className = 'px-4 py-2 text-center bg-blue-50 text-blue-700 cursor-pointer hover:bg-blue-100';
                            retryOption.textContent = '🔄 Coba lagi';
                            retryOption.addEventListener('click', function () {
                                loadAssetMasters(searchTerm);
                            });
                            assetList.appendChild(retryOption);
                            searchInput.placeholder = "Gagal memuat aset yang dapat disusutkan";
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }

                    function addAssetMasterOption(item) {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                        const assetMasterName = item.asset_name || 'Unknown';
                        const assetMasterCode = item.asset_master_code || '';

                        li.textContent = assetMasterName;
                        li.setAttribute('data-id', item.asset_master_id);
                        li.setAttribute('data-name', assetMasterName);
                        li.setAttribute('data-code', assetMasterCode);
                        li.setAttribute('data-depreciable', item.is_depreciable === true ? 'true' : 'false');

                        li.addEventListener('click', function () {
                            selectedAssetId.value = this.getAttribute('data-name');
                            searchInput.value = this.getAttribute('data-name');
                            dropdown.classList.add('hidden');
                        });

                        assetList.appendChild(li);
                    }
                }

                initBuildingSearch();
                initRoomSearch();
                initAssetMasterSearch();

                const depreciationFilterForm = document.getElementById('depreciationFilterForm');
                if (depreciationFilterForm) {
                    depreciationFilterForm.addEventListener('submit', function (e) {
                        const searchInput = document.getElementById('asset_master_search');
                        const selectedAssetId = document.getElementById('selected_asset_master_id');

                        if (searchInput && searchInput.value && (!selectedAssetId || !selectedAssetId.value)) {
                            searchInput.value = '';
                            if (selectedAssetId) selectedAssetId.value = '';
                        }

                        const buildingIdInput = document.getElementById('selected_building_id');
                        const roomIdInput = document.getElementById('selected_room_id');
                        const subcategoryInput = document.getElementById('selected_subcategory');

                        if (buildingIdInput && buildingIdInput.name !== 'building_id') {
                            buildingIdInput.name = 'building_id';
                        }

                        if (roomIdInput && roomIdInput.name !== 'room_id') {
                            roomIdInput.name = 'room_id';
                        }

                        if (subcategoryInput) {
                            subcategoryInput.name = 'subcategory_id';
                        }

                        const asOfDateInput = document.querySelector('input[name="as_of_date"]');
                        if (asOfDateInput) {
                            const yearMonthInput = document.createElement('input');
                            yearMonthInput.type = 'hidden';
                            yearMonthInput.name = 'year_month';
                            yearMonthInput.value = asOfDateInput.value;
                            this.appendChild(yearMonthInput);
                        }
                    });
                }

                const exportBtn = document.getElementById('exportBtn');
                if (exportBtn) {
                    exportBtn.addEventListener('click', function () {
                        const exportUrl = "{{ route('report.depreciation.export-pdf') }}?" + new URLSearchParams({
                            asset_master_name: document.getElementById('asset_master_search')?.value || "",
                            building_id: document.getElementById('selected_building_id')?.value || "",
                            room_id: document.getElementById('selected_room_id')?.value || "",
                            asset_type: document.querySelector('select[name="asset_type"]')?.value || "",
                            subcategory_id: document.getElementById('selected_subcategory')?.value || "",
                            year_month: document.querySelector('input[name="as_of_date"]')?.value || "",
                            book_value_end: "true"
                        }).toString();

                        window.open(exportUrl, '_blank');
                    });
                }
            });
        </script>
    @endpush
@endsection
