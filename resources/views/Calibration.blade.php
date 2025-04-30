@extends('Layout.app')

@section('title', 'Calibration Management')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Calibration Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">CALIBRATION</h1>

                        <!-- Button Add Calibration -->
                        <button id="addCalibrationBtn"
                            class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="text-base">Add Calibration</span>
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Search by asset name or code..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <select id="statusFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="">All Status</option>
                                <option value="scheduled">scheduled</option>
                                <option value="in_progress">in_progress</option>
                                <option value="completed">completed</option>
                                <option value="cancelled">cancelled</option>
                            </select>
                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                            </select>
                            <button id="bulkDeleteBtn" class="hidden px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
                                Delete Selected
                            </button>
                        </div>
                    </div>

                    <!-- Calibration Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                        <input type="checkbox" id="selectAllCalibrations" class="checkbox checkbox-sm" />
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Task Code</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Location</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Planned Date</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calibrations ?? [] as $calibration)
                                                            <tr>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                    <input type="checkbox" class="calibration-checkbox checkbox checkbox-sm" data-id="{{ $calibration['id'] }}" />
                                                                </td>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $calibration['task_code'] ?? '-' }}
                                                                </td>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                    <div class="flex flex-col">
                                                                        <span class="font-medium">{{ $calibration['asset_name'] ?? '-' }}</span>
                                                                        <span class="text-gray-500">{{ $calibration['asset_code'] ?? '-' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                    @if(isset($calibration['location']))
                                                                        <div class="flex flex-col">
                                                                            <span>{{ $calibration['location']['room_name'] ?? '-' }}</span>
                                                    <span class="text-gray-500">{{ $calibration['location']['building_name'] ?? '-' }}</span>
                                                                        </div>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                    {{ $calibration['planning_calibration_date'] ? date('d M Y', strtotime($calibration['planning_calibration_date'])) : '-' }}
                                                                </td>
                                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                    @php
                                                                        $statusClass = '';
                                                                        $status = $calibration['status_calibration'] ?? '';

                                                                        if ($status == 'scheduled') {
                                                                            $statusClass = 'bg-blue-100 text-blue-800';
                                                                        } elseif ($status == 'in_progress') {
                                                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                                                        } elseif ($status == 'completed') {
                                                                            $statusClass = 'bg-green-100 text-green-800';
                                                                        } elseif ($status == 'overdue') {
                                                                            $statusClass = 'bg-red-100 text-red-800';
                                                                        }
                                                                    @endphp
                                                                    <span class="px-2 py-1 rounded text-xs {{ $statusClass }}">
                                                                        {{ ucfirst($status) ?: '-' }}
                                                                    </span>
                                                                </td>
                                                                <td class="p-3 border-t border-[#EEF1F4]">
                                                                    <div class="flex justify-center gap-2">
                                                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-calibration-btn"
                                                                            data-id="{{ $calibration['id'] }}">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                            </svg>
                                                                        </button>
                                                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-calibration-btn {{ in_array(strtolower($calibration['status_calibration'] ?? ''), ['completed', 'approved']) ? 'hidden' : '' }}"
                                                                            data-id="{{ $calibration['id'] }}">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                            </svg>
                                                                        </button>
                                                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-calibration-btn"
                                                                            data-id="{{ $calibration['id'] }}">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No
                                            calibrations found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ isset($pagination['has_prev']) && $pagination['has_prev'] ? request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) : '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($pagination['has_prev']) || !$pagination['has_prev'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $totalPages = $pagination['total_pages'] ?? 1;
                                    $maxPagesShown = 5; // Show max 5 pages at once
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $startPage + $maxPagesShown - 1);

                                    if ($endPage - $startPage + 1 < $maxPagesShown) {
                                        $startPage = max(1, $endPage - $maxPagesShown + 1);
                                    }
                                @endphp

                                @if($startPage > 1)
                                    <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        1
                                    </a>
                                    @if($startPage > 2)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                        class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if($endPage < $totalPages)
                                    @if($endPage < $totalPages - 1)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $totalPages }}
                                    </a>
                                @endif
                            </div>
                            <a href="{{ isset($pagination['has_next']) && $pagination['has_next'] ? request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) : '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($pagination['has_next']) || !$pagination['has_next'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="flex items-center gap-2 mt-4 md:mt-0">
                            <span class="text-sm text-gray-600">
                                @if(isset($pagination) && isset($pagination['total_items']))
                                    Showing {{ ($pagination['current_page'] - 1) * $pagination['limit'] + 1 }}
                                    to {{ min($pagination['current_page'] * $pagination['limit'], $pagination['total_items']) }}
                                    of {{ $pagination['total_items'] }} entries
                                @else
                                    Showing 0 to 0 of 0 entries
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changePerPage(this.value)">
                                <option value="10" {{ (isset($pagination['limit']) && $pagination['limit'] == 10) ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ (isset($pagination['limit']) && $pagination['limit'] == 25) ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ (isset($pagination['limit']) && $pagination['limit'] == 50) ? 'selected' : '' }}>50 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Calibration Modal - Changed to Perform Calibration Modal -->
        <div id="viewCalibrationModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="viewCalibrationModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">PERFORM CALIBRATION</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="viewCalibrationModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <form id="updateCalibrationForm" class="space-y-6" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="calibration_id" name="calibration_id">

                                <!-- Required fields note -->
                                <div class="text-sm text-gray-600 mb-4">
                                    Field marked <span class="text-red-500">*</span> are required to fill or mandatory
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Left Column -->
                                    <div class="space-y-5">
                                        <!-- Planning Date -->
                                        <div>
                                            <label for="planning_calibration_date"
                                                class="block text-sm font-medium text-gray-700">
                                                PLANNING DATE<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="planning_date_display" name="planning_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Work Date (Actual Calibration Date) -->
                                        <div>
                                            <label for="actual_calibration_date"
                                                class="block text-sm font-medium text-gray-700">
                                                WORK DATE<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="actual_calibration_date" name="actual_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                required>
                                        </div>

                                        <!-- Next Calibration Date -->
                                        <div>
                                            <label for="next_calibration_date"
                                                class="block text-sm font-medium text-gray-700">
                                                NEXT CALIBRATION<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="next_calibration_date" name="next_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                required>
                                        </div>

                                        <!-- Asset Code -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">ASSET CODE</label>
                                            <input type="text" id="asset_code_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Asset Name -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">ASSET NAME</label>
                                            <input type="text" id="asset_name_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Certificate Number -->
                                        <div>
                                            <label for="certificate_number" class="block text-sm font-medium text-gray-700">
                                                CERTIFICATES NUMBER<span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="certificate_number" name="certificate_number"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-5">
                                        <!-- Serial Number -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">SERIAL NUMBER</label>
                                            <input type="text" id="serial_number_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Brand (Merk) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">MERK</label>
                                            <input type="text" id="brand_name_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Type -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">TYPE</label>
                                            <input type="text" id="model_number_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Location -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">LOCATION</label>
                                            <input type="text" id="location_display"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Vendor -->
                                        <div>
                                            <label for="vendor_id" class="block text-sm font-medium text-gray-700">
                                                VENDOR
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="vendor_search" placeholder="Search vendor..."
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                <input type="hidden" id="vendor_id" name="vendor_id">
                                                <div id="vendor_results" class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto border border-gray-300"></div>
                                        </div>
                                        </div>

                                        <!-- Service Price -->
                                        <div>
                                            <label for="calibration_price" class="block text-sm font-medium text-gray-700">
                                                SERVICE PRICE
                                            </label>
                                            <input type="number" id="calibration_price" name="calibration_price" step="0.01"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                        </div>
                                    </div>
                                        </div>

                                <!-- Full Width Fields -->
                                <div class="mt-6 space-y-5">
                                        <!-- Result -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">
                                            HASIL<span class="text-red-500">*</span>
                                            </label>
                                        <div class="mt-2 flex flex-wrap gap-6">
                                                <div class="flex items-center">
                                                    <input type="radio" id="result_pass" name="calibration_result"
                                                        value="pass" class="h-4 w-4 text-[#213268] focus:ring-[#213268]"
                                                        required>
                                                <label for="result_pass" class="ml-2 text-sm text-gray-700">Lulus</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="result_fail" name="calibration_result"
                                                        value="fail" class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                <label for="result_fail" class="ml-2 text-sm text-gray-700">Gagal</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="result_unknown" name="calibration_result"
                                                        value="unknown" class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                <label for="result_unknown" class="ml-2 text-sm text-gray-700">Tidak
                                                    Ditemukan</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Document File -->
                                        <div>
                                        <label class="block text-sm font-medium text-gray-700">UPLOADED FILES</label>
                                        <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- File preview container -->
                                            <div id="file-preview" class="mt-2 mb-4 w-full hidden">
                                                <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <!-- Image preview -->
                                                    <img id="image-preview" class="w-full h-auto max-h-64 object-contain mx-auto rounded hidden" alt="File preview">

                                                    <!-- PDF/File preview -->
                                                    <div id="file-info" class="flex items-center">
                                                        <svg class="w-6 h-6 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span id="file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="remove-file" class="ml-auto text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Drag your file or <span class="text-[#213268] font-semibold">browse files</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                            </div>
                                            <input type="file" id="document_file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                        <!-- Calibration Notes - Full Width -->
                                        <div>
                                            <label for="notes" class="block text-sm font-medium text-gray-700">CALIBRATION
                                                NOTES</label>
                                            <textarea id="notes" name="notes" rows="3"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"></textarea>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <input type="hidden" id="status_calibration" name="status_calibration" value="completed">

                                        <div class="pt-4 flex justify-end gap-4">
                                            <button type="button"
                                                class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200"
                                                data-modal="viewCalibrationModal">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transition-colors duration-200">
                                                Save Calibration
                                            </button>
                                        </div>
                                    </form>
                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div id="deleteCalibrationModal" class="fixed inset-0 z-50 hidden">
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                        <div class="fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                                    id="deleteCalibrationModalContent">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center p-6 pb-0">
                                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE CALIBRATION</h2>
                                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                            data-modal="deleteCalibrationModal">
                                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Form -->
                                    <form id="deleteCalibrationForm" method="POST">
                                        @csrf
                                    <div class="p-6">
                                            <div class="space-y-6 max-w-[400px] mx-auto">
                                                <div class="flex flex-col items-center">
                                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                                    <p class="text-base text-gray-600 text-center">Are you sure you want to delete this calibration record? This action cannot be undone.</p>
                                                    <p id="deleteCalibrationName" class="text-base font-semibold text-center mt-2"></p>
                                                </div>
                                                <div class="flex gap-3">
                                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                                    data-modal="deleteCalibrationModal">
                                                    Cancel
                                                </button>
                                                    <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Calibration Modal -->
                    <div id="addCalibrationModal" class="fixed inset-0 z-50 hidden">
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                        <div class="fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[850px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                                    id="addCalibrationModalContent">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center p-6 pb-0">
                                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Add New Calibration Schedule</h2>
                                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                            data-modal="addCalibrationModal">
                                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Form -->
                                    <div class="p-6">
                                        <form id="addCalibrationForm" class="space-y-6">
                                            @csrf
                                            <!-- Required fields note -->
                                            <div class="text-sm text-gray-600">
                                                Field marked <span class="text-red-500">*</span> are required to fill or mandatory
                                            </div>

                                            <!-- Schedule Date -->
                                            <div class="bg-[#B0DAE5] p-4 rounded-lg">
                                                <div class="flex items-center gap-4">
                                                    <div class="min-w-[150px]">
                                                        <label class="block text-base font-semibold">
                                                            SCHEDULE START<span class="text-red-500">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="flex-1">
                                                        <input type="date" name="planning_calibration_date"
                                                            id="planning_calibration_date"
                                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                            required>
                                                    </div>
                                                    <div>
                                                        <button type="button" id="addAssetsBtn"
                                                            class="bg-[#4299e1] hover:bg-[#3182ce] text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                                                            <span class="text-xl mr-1">+</span>
                                                            Add Assets
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Selected Assets Table -->
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                                No</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">
                                                                AssetCode</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Asset
                                                                Name</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">
                                                                Description</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Asset
                                                                Type</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Category
                                                                Name</th>
                                                            <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-center">Action
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="selectedAssetsList">
                                                        <tr>
                                                            <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No
                                                                data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination for selected assets -->
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm text-gray-600">Show</span>
                                                <select id="selectedAssetsPerPage"
                                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                                    <option value="15">15</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                </select>
                                                <span class="text-sm text-gray-600">entries</span>
                                            </div>

                                            <!-- Button Group -->
                                            <div class="pt-4 flex justify-end gap-4">
                                                <button type="submit"
                                                    class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Selection Modal -->
                    <div id="assetSelectionModal" class="fixed inset-0 z-[60] hidden">
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                        <div class="fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[1200px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                                    id="assetSelectionModalContent">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center p-6 pb-0">
                                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Select Assets</h2>
                                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                            data-modal="assetSelectionModal">
                                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6">
                                        <!-- Search and Filter -->
                                        <div class="flex flex-col md:flex-row gap-4 mb-4">
                                            <div class="relative flex-grow">
                                                <input type="text" id="assetSearchInput"
                                                    placeholder="Search by asset name, code, or serial number..."
                                                    class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Assets Table -->
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                            <input type="checkbox" id="selectAllAssets" class="checkbox checkbox-sm">
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Code
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Type
                                                        </th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Category
                                                            Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="assetSelectionList">
                                                    <tr>
                                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                            Loading assets...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Pagination -->
                                        <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                                            <div class="flex items-center space-x-2" id="assetPaginationControls">
                                                <!-- Pagination will be inserted here -->
                                            </div>

                                            <div class="flex items-center gap-2 mt-4 md:mt-0">
                                                <span class="text-sm text-gray-600" id="assetPaginationInfo">
                                                    Showing 0 to 0 of 0 entries
                                                </span>
                                                <select id="assetPerPageSelect"
                                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                                    <option value="10">10 per page</option>
                                                    <option value="25">25 per page</option>
                                                    <option value="50">50 per page</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Button Group -->
                                        <div class="pt-4 flex justify-end gap-4">
                                            <button type="button"
                                                class="close-modal px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200"
                                                data-modal="assetSelectionModal">
                                                Cancel
                                            </button>
                                            <button type="button" id="selectAssetsBtn"
                                                class="px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                Select
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success and Error Notifications -->
                @if(session('success'))
                    <div id="successNotification"
                        class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50"
                        role="alert">
                        <div class="flex items-center">
                            <div class="py-1">
                                <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold">Berhasil!</p>
                                <p>{{ session('success') }}</p>
                            </div>
                            <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                        </div>
                    </div>

                    <script>
                        setTimeout(function () {
                            const notification = document.getElementById('successNotification');
                            if (notification) {
                                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                                setTimeout(function () {
                                    notification.remove();
                                }, 500);
                            }
                        }, 5000); // Hide after 5 seconds
                    </script>
                @endif

                @if(session('error'))
                    <div id="errorNotification"
                        class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50"
                        role="alert">
                        <div class="flex items-center">
                            <div class="py-1">
                                <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold">Gagal!</p>
                                <p>{{ session('error') }}</p>
                            </div>
                            <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                        </div>
                    </div>

                    <script>
                        setTimeout(function () {
                            const notification = document.getElementById('errorNotification');
                            if (notification) {
                                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                                setTimeout(function () {
                                    notification.remove();
                                }, 500);
                            }
                        }, 5000); // Hide after 5 seconds
                    </script>
                @endif

                @push('scripts')
                <script>
                    // Flash messages from server
                    const flashSuccess = @json(session('success') ?? null);
                    const flashError = @json(session('error') ?? null);

                    document.addEventListener('DOMContentLoaded', function () {
                        // Check for flash messages on page load that didn't trigger the toast
                        if (typeof flashSuccess !== 'undefined' && flashSuccess) {
                            showToast(flashSuccess, 'success');
                        }
                        if (typeof flashError !== 'undefined' && flashError) {
                            showToast(flashError, 'error');
                        }

                        // Mengatur tanggal minimum untuk input tanggal (tidak bisa memilih tanggal yang sudah lewat)
                        const today = new Date().toISOString().split('T')[0];

                        // Set min attribute untuk planning_calibration_date di modal add calibration
                        const planningDateInput = document.getElementById('planning_calibration_date');
                        if (planningDateInput) {
                            planningDateInput.setAttribute('min', today);
                        }

                        // Set min attribute untuk next_calibration_date di modal perform calibration
                        const nextCalibrationDateInput = document.getElementById('next_calibration_date');
                        if (nextCalibrationDateInput) {
                            nextCalibrationDateInput.setAttribute('min', today);
                        }

                        // Define a showToast function that creates notifications in the same style as the static ones
                        window.showToast = function(message, type = 'success') {
                            console.log('Showing toast:', message, type); // Debug log

                            // Remove existing notifications with the same type
                            const existingNotification = document.getElementById(type === 'success' ? 'successNotification' : 'errorNotification');
                            if (existingNotification) {
                                existingNotification.remove();
                            }

                            // Create the notification element
                            const notification = document.createElement('div');
                            notification.id = type === 'success' ? 'successNotification' : 'errorNotification';
                            notification.className = `fixed top-4 right-4 bg-${type === 'success' ? 'green' : 'red'}-100 border-l-4 border-${type === 'success' ? 'green' : 'red'}-500 text-${type === 'success' ? 'green' : 'red'}-700 p-4 rounded shadow-md z-50`;
                            notification.setAttribute('role', 'alert');

                            // Set inner HTML
                            notification.innerHTML = `
                                <div class="flex items-center">
                                    <div class="py-1">
                                        <svg class="h-6 w-6 text-${type === 'success' ? 'green' : 'red'}-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="${type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'}" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold">${type === 'success' ? 'Berhasil!' : 'Gagal!'}</p>
                                        <p>${message}</p>
                                    </div>
                                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                                </div>
                            `;

                            // Add to document
                            document.body.appendChild(notification);

                            // Auto-hide after 5 seconds
                            setTimeout(function() {
                                if (document.getElementById(notification.id)) {
                                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                                    setTimeout(function() {
                                        if (document.getElementById(notification.id)) {
                                            notification.remove();
                                        }
                                    }, 500);
                                }
                            }, 5000);

                            return notification;
                        };

                        // Show flash messages with the showToast function
                        @if(session('success'))
                        showToast("{{ session('success') }}", 'success');
                        @endif

                        @if(session('error'))
                        showToast("{{ session('error') }}", 'error');
                        @endif

                        // Handle 'Select All' checkbox for calibrations table
                        const selectAllCalibrations = document.getElementById('selectAllCalibrations');
                        if (selectAllCalibrations) {
                            selectAllCalibrations.addEventListener('change', function() {
                                const isChecked = this.checked;
                                document.querySelectorAll('.calibration-checkbox').forEach(checkbox => {
                                    checkbox.checked = isChecked;
                                });
                                updateBulkDeleteButtonVisibility();
                            });

                            // Update "Select All" checkbox state based on individual checkboxes
                            document.addEventListener('change', function(e) {
                                if (e.target.classList.contains('calibration-checkbox')) {
                                    const allCheckboxes = document.querySelectorAll('.calibration-checkbox');
                                    const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                                    selectAllCalibrations.checked = allCheckboxes.length === checkedCheckboxes.length;
                                    selectAllCalibrations.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                                    updateBulkDeleteButtonVisibility();
                                }
                            });
                        }

                        // Handle bulk delete button visibility and functionality
                        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                        function updateBulkDeleteButtonVisibility() {
                            const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                            if (checkedCheckboxes.length > 0) {
                                bulkDeleteBtn.classList.remove('hidden');
                            } else {
                                bulkDeleteBtn.classList.add('hidden');
                            }
                        }

                        // Handle bulk delete action
                        if (bulkDeleteBtn) {
                            bulkDeleteBtn.addEventListener('click', function() {
                                const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                                if (checkedCheckboxes.length === 0) {
                                    showToast('No calibrations selected', 'error');
                                    return;
                                }

                                // Get the IDs of the selected calibrations
                                const selectedIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));

                                // Open confirmation modal with count information
                                const deleteCalibrationName = document.getElementById('deleteCalibrationName');
                                if (deleteCalibrationName) {
                                    deleteCalibrationName.textContent = `${selectedIds.length} selected calibration records`;
                                }

                                // Set up the form for bulk delete
                                const calibrationForm = document.getElementById('deleteCalibrationForm');
                                if (calibrationForm) {
                                    // Set form action to the correct URL using the named route
                                    calibrationForm.action = "{{ route('calibrations.bulk.delete') }}";

                                    // Store the IDs in a hidden input - we'll use this in the submit handler
                                    let hiddenInput = document.getElementById('delete_calibration_id');
                                    if (!hiddenInput) {
                                        hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.id = 'delete_calibration_id';
                                        calibrationForm.appendChild(hiddenInput);
                                    }

                                    // Store as comma-separated string - the submit handler will convert to array
                                    hiddenInput.value = selectedIds.join(',');

                                    console.log('Preparing bulk delete for IDs:', selectedIds);

                                    // Open delete confirmation modal
                                    openModal(modals.delete, modalContents.delete);
                                }
                            });
                        }

                        // Function to change items per page
                        window.changePerPage = function (limit) {
                            const url = new URL(window.location.href);
                            url.searchParams.set('limit', limit);
                            window.location.href = url.toString();
                        }

                        // Status filter - apply immediately on change
                        document.getElementById('statusFilter').addEventListener('change', function() {
                            applyFilters();
                        });

                        // Sort order - apply immediately on change
                        document.getElementById('sortOrder').addEventListener('change', function() {
                            applyFilters();
                        });

                        // Function to apply all filters and sorting
                        function applyFilters() {
                            const searchTerm = document.getElementById('searchInput').value;
                            const statusFilter = document.getElementById('statusFilter').value;
                            const sortOrder = document.getElementById('sortOrder').value;

                            const url = new URL(window.location.href);

                            // Set search parameter
                            if (searchTerm) url.searchParams.set('search', searchTerm);
                            else url.searchParams.delete('search');

                            // Set status parameter
                            if (statusFilter) url.searchParams.set('status', statusFilter);
                            else url.searchParams.delete('status');

                            // Set sort parameter
                            if (sortOrder) url.searchParams.set('sort', sortOrder);
                            else url.searchParams.delete('sort');

                            // Reset to first page on filter change
                            url.searchParams.set('page', 1);

                            // Redirect to new URL with filters
                            window.location.href = url.toString();
                        }

                        // Search input - apply filters on debounce
                        document.getElementById('searchInput')?.addEventListener('input', debounce(function() {
                            applyFilters();
                        }, 500));

                        // Set existing sort value from URL
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('sort')) {
                            document.getElementById('sortOrder').value = urlParams.get('sort');
                        }

                        // Remove the old event listener and filterBtn click handler since we don't need them anymore

                        // Modal handling
                        const modals = {
                            view: document.getElementById('viewCalibrationModal'),
                            add: document.getElementById('addCalibrationModal'),
                            asset: document.getElementById('assetSelectionModal'),
                            delete: document.getElementById('deleteCalibrationModal')
                        };

                        const modalContents = {
                            view: document.getElementById('viewCalibrationModalContent'),
                            add: document.getElementById('addCalibrationModalContent'),
                            asset: document.getElementById('assetSelectionModalContent'),
                            delete: document.getElementById('deleteCalibrationModalContent')
                        };

                        // Function to open modal
                        function openModal(modal, content) {
                            modal.classList.remove('hidden');
                            setTimeout(() => {
                                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                            }, 10);
                        }

                        // Function to close modal
                        function closeModal(modal, content) {
                            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                            }, 300);
                        }

                        // Close modal buttons
                        document.querySelectorAll('.close-modal').forEach(button => {
                            button.addEventListener('click', () => {
                                const modalId = button.getAttribute('data-modal');
                                const modal = document.getElementById(modalId);
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                            });
                        });

                        // Add Calibration Button Click Handler
                        document.getElementById('addCalibrationBtn').addEventListener('click', function() {
                            // Set min date untuk planning_calibration_date setiap kali modal dibuka
                            const today = new Date().toISOString().split('T')[0];
                            const planningDateInput = document.getElementById('planning_calibration_date');
                            if (planningDateInput) {
                                planningDateInput.setAttribute('min', today);
                            }

                            openModal(modals.add, modalContents.add);
                        });

                        // Load vendors for dropdown
                        function loadVendors() {
                            console.log('Loading vendors...');
                            fetch('/vendor?json=true', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => {
                                    console.log('Vendor API response status:', response.status);
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Vendor data received:', data);
                                    const select = document.getElementById('vendor_id');
                                    select.innerHTML = '<option value="">Select Vendor</option>';

                                    if (Array.isArray(data)) {
                                        console.log('Processing vendors as array, length:', data.length);
                                        data.forEach(vendor => {
                                            const option = document.createElement('option');
                                            option.value = vendor.vendor_id;
                                            option.textContent = vendor.vendor_name;
                                            select.appendChild(option);
                                        });
                                    } else if (data.vendors && Array.isArray(data.vendors)) {
                                        console.log('Processing vendors as data.vendors array, length:', data.vendors.length);
                                        data.vendors.forEach(vendor => {
                                            const option = document.createElement('option');
                                            option.value = vendor.vendor_id;
                                            option.textContent = vendor.vendor_name;
                                            select.appendChild(option);
                                        });
                                    } else {
                                        console.error('Unexpected vendor data format:', data);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading vendors:', error);
                                });
                        }

                        // Edit calibration buttons
                        document.querySelectorAll('.edit-calibration-btn').forEach(button => {
                            button.addEventListener('click', function () {
                                const calibrationId = this.getAttribute('data-id');
                                document.getElementById('calibration_id').value = calibrationId;

                                // Fetch calibration details
                                fetch(`/calibrations/${calibrationId}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            const calibration = data.data;

                                            // Check if status is completed or approved and prevent editing
                                            const status = (calibration.status_calibration || '').toLowerCase();
                                            if (status === 'completed' || status === 'approved') {
                                                // If this is the edit pencil button (not the view button), prevent opening
                                                if (this.querySelector('path[d*="3.536"]')) {
                                                    showToast('Calibration has been completed and cannot be modified', 'error');
                                                    return;
                                                }
                                            }

                                            // Set current date as work date by default when modal opens
                                            const today = new Date().toISOString().split('T')[0];
                                            document.getElementById('actual_calibration_date').value = today;

                                            // Set min date untuk next_calibration_date setiap kali modal dibuka
                                            const nextCalibrationDateInput = document.getElementById('next_calibration_date');
                                            if (nextCalibrationDateInput) {
                                                nextCalibrationDateInput.setAttribute('min', today);
                                            }

                                            // Set read-only display fields
                                            document.getElementById('planning_date_display').value = calibration.planning_calibration_date || '';
                                            document.getElementById('asset_code_display').value = calibration.asset_code || '-';
                                            document.getElementById('asset_name_display').value = calibration.asset_name || '-';
                                            document.getElementById('brand_name_display').value = calibration.brand_name || '-';
                                            document.getElementById('model_number_display').value = calibration.model_number || '-';
                                            document.getElementById('serial_number_display').value = calibration.serial_number || '-';

                                            // Set location display
                                            let locationText = '-';
                                            if (calibration.location) {
                                                const locationParts = [];
                                                if (calibration.location.room_name) locationParts.push(calibration.location.room_name);
                                                if (calibration.location.floor_number) locationParts.push(calibration.location.floor_number);
                                                if (calibration.location.building_name) locationParts.push(calibration.location.building_name);
                                                if (locationParts.length > 0) {
                                                    locationText = locationParts.join(' | ');
                                                }
                                            }
                                            document.getElementById('location_display').value = locationText;

                                            // Set vendor search value if vendor exists
                                            if (calibration.vendor_id) {
                                                document.getElementById('vendor_id').value = calibration.vendor_id;
                                                document.getElementById('vendor_search').value = calibration.vendor_name || '';
                                            } else {
                                                document.getElementById('vendor_id').value = '';
                                                document.getElementById('vendor_search').value = '';
                                            }

                                            // Only set next_calibration_date if it exists in the data
                                            if (calibration.next_calibration_date) {
                                                document.getElementById('next_calibration_date').value = calibration.next_calibration_date;
                                            }

                                            document.getElementById('certificate_number').value = calibration.certificate_number || '';
                                            document.getElementById('calibration_price').value = calibration.calibration_price || '';
                                            document.getElementById('notes').value = calibration.notes || '';

                                            // Set radio button for result
                                            if (calibration.calibration_result === 'pass') {
                                                document.getElementById('result_pass').checked = true;
                                            } else if (calibration.calibration_result === 'fail') {
                                                document.getElementById('result_fail').checked = true;
                                            } else if (calibration.calibration_result === 'unknown') {
                                                document.getElementById('result_unknown').checked = true;
                                            }

                                            // Always hide file previews when opening modal
                                            const filePreview = document.getElementById('file-preview');
                                            if (filePreview) filePreview.classList.add('hidden');

                                            const imagePreview = document.getElementById('image-preview');
                                            if (imagePreview) {
                                                imagePreview.src = '';
                                                imagePreview.classList.add('hidden');
                                            }

                                            const fileInfo = document.getElementById('file-info');
                                            if (fileInfo) fileInfo.classList.remove('hidden');

                                            // If status is completed or approved, disable form fields
                                            const isCompleted = status === 'completed' || status === 'approved';
                                            const form = document.getElementById('updateCalibrationForm');
                                            const formElements = form.querySelectorAll('input, select, textarea, button[type="submit"]');

                                            formElements.forEach(element => {
                                                if (element.id !== 'planning_date_display' &&
                                                    element.id !== 'asset_code_display' &&
                                                    element.id !== 'asset_name_display' &&
                                                    element.id !== 'brand_name_display' &&
                                                    element.id !== 'model_number_display' &&
                                                    element.id !== 'serial_number_display' &&
                                                    element.id !== 'location_display') {

                                                    if (isCompleted) {
                                                        element.setAttribute('disabled', 'disabled');
                                                        if (element.tagName === 'BUTTON' && element.type === 'submit') {
                                                            element.classList.add('bg-gray-500');
                                                            element.classList.remove('bg-[#213268]', 'hover:bg-[#152349]');
                                                        }
                                    } else {
                                                        element.removeAttribute('disabled');
                                                        if (element.tagName === 'BUTTON' && element.type === 'submit') {
                                                            element.classList.remove('bg-gray-500');
                                                            element.classList.add('bg-[#213268]', 'hover:bg-[#152349]');
                                                        }
                                                    }
                                                }
                                            });

                                            // Add info message at the top of the form if completed
                                            const infoMessageContainer = document.querySelector('#viewCalibrationModalContent .p-6');
                                            const existingInfoMessage = document.getElementById('completed-info-message');

                                            if (existingInfoMessage) {
                                                existingInfoMessage.remove();
                                            }

                                            if (isCompleted) {
                                                const infoMessage = document.createElement('div');
                                                infoMessage.id = 'completed-info-message';
                                                infoMessage.className = 'bg-blue-50 border-l-4 border-blue-500 p-4 mb-4';
                                                infoMessage.innerHTML = `
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 text-blue-500">
                                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm text-blue-700">
                                                                This calibration is marked as ${status.toUpperCase()}. Form is in read-only mode.
                                                            </p>
                                                        </div>
                                                    </div>
                                                `;
                                                infoMessageContainer.insertAdjacentElement('afterbegin', infoMessage);
                                            }

                                            // Open modal
                                            openModal(modals.view, modalContents.view);
                                        } else {
                                            showToast('Failed to load calibration details', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showToast('An error occurred while fetching calibration details', 'error');
                                    });
                            });
                        });

                        // Add file upload functionality
                        document.getElementById('document_file')?.addEventListener('change', function() {
                            const file = this.files[0];
                            if (file) {
                                // Show preview container
                                const filePreview = document.getElementById('file-preview');
                                const imagePreview = document.getElementById('image-preview');
                                const fileInfo = document.getElementById('file-info');
                                const fileNameText = document.getElementById('file-name-text');

                                if (filePreview) filePreview.classList.remove('hidden');

                                // Set file name
                                if (fileNameText) fileNameText.textContent = file.name;

                                // Check if file is an image
                                if (file.type.startsWith('image/')) {
                                    // Show image preview, hide file info
                                    if (imagePreview) {
                                        const objectUrl = URL.createObjectURL(file);
                                        imagePreview.src = objectUrl;
                                        imagePreview.classList.remove('hidden');
                                    }
                                    if (fileInfo) fileInfo.classList.add('hidden');
                                } else {
                                    // Show file info, hide image preview
                                    if (fileInfo) fileInfo.classList.remove('hidden');
                                    if (imagePreview) imagePreview.classList.add('hidden');
                                }
                            }
                        });

                        // Remove selected file
                        document.getElementById('remove-file')?.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            // Reset file input
                            const fileInput = document.getElementById('document_file');
                            if (fileInput) fileInput.value = '';

                            // Hide preview
                            const filePreview = document.getElementById('file-preview');
                            if (filePreview) filePreview.classList.add('hidden');

                            // Clean up image preview URL if it exists
                            const imagePreview = document.getElementById('image-preview');
                            if (imagePreview && imagePreview.src) {
                                URL.revokeObjectURL(imagePreview.src);
                                imagePreview.src = '';
                            }
                        });

                        // Delete calibration buttons
                        document.querySelectorAll('.delete-calibration-btn').forEach(button => {
                            button.addEventListener('click', function () {
                                const calibrationId = this.getAttribute('data-id');
                                const calibrationForm = document.getElementById('deleteCalibrationForm');
                                const deleteCalibrationName = document.getElementById('deleteCalibrationName');

                                // Get calibration details to show in the confirmation modal
                                const assetName = this.closest('tr').querySelector('td:nth-child(3) .font-medium').textContent;
                                const assetCode = this.closest('tr').querySelector('td:nth-child(3) .text-gray-500').textContent;

                                // Set form action to the correct URL using the named route
                                calibrationForm.action = "{{ route('calibrations.bulk.delete') }}";

                                // Add hidden input for the calibration ID - without setting the name attribute
                                let hiddenInput = document.getElementById('delete_calibration_id');
                                if (!hiddenInput) {
                                    hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.id = 'delete_calibration_id';
                                    calibrationForm.appendChild(hiddenInput);
                                }
                                hiddenInput.value = calibrationId;

                                // Set calibration name in the modal
                                deleteCalibrationName.textContent = `${assetName} (${assetCode})`;

                                // Open delete confirmation modal
                                openModal(modals.delete, modalContents.delete);
                            });
                        });

                        // Form submission handler for delete
                        document.getElementById('deleteCalibrationForm').addEventListener('submit', function(e) {
                            e.preventDefault();

                            const form = this;
                            const calibrationIdInput = document.getElementById('delete_calibration_id').value;

                            // Check if the value contains a comma, which indicates multiple IDs
                            const isMultiple = calibrationIdInput.includes(',');
                            let ids = [];

                            if (isMultiple) {
                                // For multiple calibrations, split the comma-separated string
                                ids = calibrationIdInput.split(',').map(id => parseInt(id.trim()));
                            } else {
                                // For single calibration, create array with one element
                                ids = [parseInt(calibrationIdInput)];
                            }

                            // Log the request data for debugging
                            console.log('Sending delete request with IDs:', ids);

                            // Make the DELETE request to the server
                            fetch("{{ route('calibrations.bulk.delete') }}", {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ ids }) // Simple payload with just the IDs array
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        console.error('Server error response:', data);
                                        throw new Error(data.message || `Server responded with status ${response.status}`);
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Close the modal
                                closeModal(modals.delete, modalContents.delete);

                                if (data.success) {
                                    // Show toast notification first
                                    showToast(data.message || 'Calibrations deleted successfully', 'success');

                                    // Delay the redirect slightly to allow the toast to be seen
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    showToast(data.message || 'Failed to delete calibration', 'error');
                                    console.error('Delete error:', data.errors);
                                }
                            })
                            .catch(error => {
                                console.error('Delete request failed:', error);
                                closeModal(modals.delete, modalContents.delete);
                                showToast(error.message || 'An error occurred while deleting the calibration', 'error');
                            });
                        });

                        // Update Calibration Form Submit
                        document.getElementById('updateCalibrationForm').addEventListener('submit', function (e) {
                            e.preventDefault();

                            const calibrationId = document.getElementById('calibration_id').value;
                            const formData = new FormData(this);

                            // Remove the calibration_id from form data since it's used in the URL
                            formData.delete('calibration_id');

                            // Add _method field for PUT request
                            formData.append('_method', 'PUT');

                            fetch(`/calibrations/${calibrationId}`, {
                                method: 'POST',  // FormData needs to be sent as POST even though we're doing a PUT
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Close the modal
                                        closeModal(modals.view, modalContents.view);

                                        // Show toast notification first
                                        showToast(data.message || 'Calibration updated successfully', 'success');

                                        // Delay the redirect slightly to allow the toast to be seen
                                        setTimeout(() => {
                                            window.location.href = "{{ route('calibration') }}";
                                        }, 1000);
                                    } else {
                                        showToast(data.message || 'Failed to update calibration', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showToast('An error occurred while updating the calibration', 'error');
                                });
                        });

                        // Selected Assets Management
                        let selectedAssets = [];

                        // Add Assets Button
                        document.getElementById('addAssetsBtn')?.addEventListener('click', function () {
                            // Open asset selection modal
                            openModal(document.getElementById('assetSelectionModal'), document.getElementById('assetSelectionModalContent'));
                            // Load assets
                            loadAssets();
                        });

                        // Debounce function to limit how often a function can be called
                        function debounce(func, wait) {
                            let timeout;
                            return function () {
                                const context = this;
                                const args = arguments;
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {
                                    func.apply(context, args);
                                }, wait);
                            };
                        }

                        // Handle search input with debounce
                        document.getElementById('assetSearchInput')?.addEventListener('input', debounce(function () {
                            loadAssets(1);
                        }, 500));

                        // Load assets for selection
                        function loadAssets(page = 1) {
                            const searchTerm = document.getElementById('assetSearchInput').value;
                            const limit = document.getElementById('assetPerPageSelect').value;

                            // Show loading state
                            document.getElementById('assetSelectionList').innerHTML = `
                                                                                                                                            <tr>
                                                                                                                                                <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Loading assets...</td>
                                                                                                                                            </tr>
                                                                                                                                    `;

                            // Fetch assets from API
                            fetch(`/assets/data?page=${page}&limit=${limit}&search=${encodeURIComponent(searchTerm)}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    const assets = data.assets || [];
                                    // Debug
                                    console.log("First asset:", assets.length > 0 ? assets[0] : "No assets");

                                    if (assets.length === 0) {
                                        document.getElementById('assetSelectionList').innerHTML = `
                                                                                                                        <tr>
                                                                                                                            <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No assets found</td>
                                                                                                                        </tr>
                                                                                                                    `;
                                        return;
                                    }

                                    // Render assets
                                    let html = '';
                                    assets.forEach(asset => {
                                        const isSelected = selectedAssets.some(selectedAsset => selectedAsset.asset_id === asset.asset_id);

                                        // Extract subcategory name properly
                                        let subcategoryName = '-';
                                        try {
                                            if (asset.subcategory) {
                                                subcategoryName = asset.subcategory.subcategory_name || '-';
                                            }
                                        } catch (e) {
                                            console.error("Error getting subcategory name:", e);
                                        }

                                        // Get asset type
                                        const assetType = asset.subcategory ? asset.subcategory.asset_type || '-' : '-';

                                        html += `
                                                                                                                        <tr>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                                                                                <input type="checkbox" class="asset-checkbox" value="${asset.asset_id}"
                                                                                                                                    data-asset-id="${asset.asset_id}"
                                                                                                                                    data-asset-name="${asset.asset_name || ''}"
                                                                                                                                    data-asset-code="${asset.asset_code || ''}"
                                                                                                                                    data-asset-description="${asset.description || ''}"
                                                                                                                                    data-asset-type="${assetType}"
                                                                                                                                    data-category-name="${subcategoryName}"
                                                                                                                                    ${isSelected ? 'checked' : ''}>
                                                                                                                            </td>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_code || '-'}</td>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                                                                                <div class="flex flex-col">
                                                                                                                                    <span class="font-medium">${asset.asset_name || '-'}</span>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.description || '-'}</td>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${assetType}</td>
                                                                                                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${subcategoryName}</td>
                                                                                                                        </tr>
                                                                                                                    `;
                                    });

                                    document.getElementById('assetSelectionList').innerHTML = html;

                                    // Setup pagination and event handlers
                                    setupAssetPagination(data.assets_pagination);
                                    attachCheckboxHandlers();
                                })
                                .catch(error => {
                                    console.error('Error loading assets:', error);
                                    document.getElementById('assetSelectionList').innerHTML = `
                                                                                                                    <tr>
                                                                                                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Error loading assets</td>
                                                                                                                    </tr>
                                                                                                                `;
                    });
            }

            // Handle this separate function to handle checkbox events
            function attachCheckboxHandlers() {
                const checkboxes = document.querySelectorAll('.asset-checkbox');

                // First remove any existing event listeners
                checkboxes.forEach(checkbox => {
                    const newCheckbox = checkbox.cloneNode(true);
                    checkbox.parentNode.replaceChild(newCheckbox, checkbox);
                });

                // Now add fresh event listeners
                document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                    checkbox.onclick = function () {
                        const assetId = parseInt(this.getAttribute('data-asset-id'));

                        // Always remove the asset first to avoid any potential duplicates
                        selectedAssets = selectedAssets.filter(asset => asset.asset_id !== assetId);

                        // Then add it back if checked
                        if (this.checked) {
                            const assetName = this.getAttribute('data-asset-name');
                            const assetCode = this.getAttribute('data-asset-code');
                            const description = this.getAttribute('data-asset-description');
                            const assetType = this.getAttribute('data-asset-type');
                            const categoryName = this.getAttribute('data-category-name');

                            selectedAssets.push({
                                asset_id: assetId,
                                asset_name: assetName,
                                asset_code: assetCode,
                                description: description,
                                asset_type: assetType,
                                subcategory_name: categoryName
                            });
                        }

                        console.log("Updated selectedAssets:", selectedAssets.length, "items");
                    };
                });

                // Handle Select All checkbox
                const selectAllCheckbox = document.getElementById('selectAllAssets');
                if (selectAllCheckbox) {
                    const newSelectAll = selectAllCheckbox.cloneNode(true);
                    selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);

                    document.getElementById('selectAllAssets').onclick = function () {
                        const checkboxes = document.querySelectorAll('.asset-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;

                            // Manually trigger the checkbox's onclick event
                            if (checkbox.onclick) checkbox.onclick();
                        });
                    };
                }
            }

            // Setup asset pagination
            function setupAssetPagination(pagination) {
                if (!pagination) return;

                const currentPage = pagination.current_page;
                const lastPage = pagination.last_page;
                const from = pagination.from;
                const to = pagination.to;
                const total = pagination.total;

                // Update pagination info
                document.getElementById('assetPaginationInfo').textContent = `Showing ${from} to ${to} of ${total} entries`;

                // Generate pagination controls
                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                                                                                                                <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                                                                                                   onclick="${currentPage > 1 ? 'loadAssets(' + (currentPage - 1) + '); return false;' : 'return false;'}">
                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                                                                                    </svg>
                                                                                                                    Prev
                                                                                                                </a>
                                                                                                            `;

                // Page numbers
                paginationHtml += '<div class="flex gap-2">';

                const maxPagesShown = 5;
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(lastPage, startPage + maxPagesShown - 1);

                if (endPage - startPage + 1 < maxPagesShown) {
                    startPage = Math.max(1, endPage - maxPagesShown + 1);
                }

                if (startPage > 1) {
                    paginationHtml += `
                                                                                                                    <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                                                                       onclick="loadAssets(${startPage}); return false;">${startPage}</a>
                                                                                                                `;

                    if (startPage > 2) {
                        paginationHtml += '<span class="flex items-center justify-center">...</span>';
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                                                                                                                    <a href="#" class="h-8 w-8 flex items-center justify-center border ${i === currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]'} rounded"
                                                                                                                       onclick="loadAssets(${i}); return false;">${i}</a>
                                                                                                                `;
                }

                if (endPage < lastPage) {
                    if (endPage < lastPage - 1) {
                        paginationHtml += '<span class="flex items-center justify-center">...</span>';
                    }

                    paginationHtml += `
                                                                                                                    <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                                                                       onclick="loadAssets(${endPage}); return false;">${endPage}</a>
                                                                                                                `;
                }

                paginationHtml += '</div>';

                // Next button
                paginationHtml += `
                                                                                                                <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage >= lastPage ? 'opacity-50 cursor-not-allowed' : ''}"
                                                                                                                   onclick="${currentPage < lastPage ? 'loadAssets(' + (currentPage + 1) + '); return false;' : 'return false;'}">
                                                                                                                    Next
                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                                                                    </svg>
                                                                                                                </a>
                                                                                                            `;

                document.getElementById('assetPaginationControls').innerHTML = paginationHtml;
            }

            // Select Assets Button
            document.getElementById('selectAssetsBtn')?.addEventListener('click', function () {
                // Check for duplicate assets and deduplicate the array
                const uniqueAssetIds = [...new Set(selectedAssets.map(asset => asset.asset_id))];
                if (uniqueAssetIds.length < selectedAssets.length) {
                    console.log("Deduplicating selectedAssets array");

                    const uniqueAssets = [];
                    const seenIds = new Set();

                    // Keep only the first occurrence of each asset
                    selectedAssets.forEach(asset => {
                        if (!seenIds.has(asset.asset_id)) {
                            uniqueAssets.push(asset);
                            seenIds.add(asset.asset_id);
                        }
                    });

                    // Update the selectedAssets array
                    selectedAssets = uniqueAssets;
                }

                // Close the asset selection modal
                closeModal(document.getElementById('assetSelectionModal'), document.getElementById('assetSelectionModalContent'));

                // Log the selected assets before updating the table
                console.log("Selected assets before updating table:", JSON.stringify(selectedAssets));

                // Update the selected assets table
                updateSelectedAssetsTable();
            });

            // Update the selected assets table
            function updateSelectedAssetsTable() {
                if (selectedAssets.length === 0) {
                    document.getElementById('selectedAssetsList').innerHTML = `
                                                                                                                    <tr>
                                                                                                                        <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No data available in table</td>
                                                                                                                    </tr>
                                                                                                                `;
                    return;
                }

                let html = '';
                selectedAssets.forEach((asset, index) => {
                    html += `
                                                                                                                    <tr>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">${index + 1}</td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_code || '-'}</td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                                                                            <div class="flex flex-col">
                                                                                                                                <span class="font-medium">${asset.asset_name || '-'}</span>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.description || '-'}</td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_type || '-'}</td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.subcategory_name || '-'}</td>
                                                                                                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                                                                            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeSelectedAsset(${asset.asset_id})">
                                                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                                                                </svg>
                                                                                                                            </button>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                `;
                });

                document.getElementById('selectedAssetsList').innerHTML = html;
            }

            // Remove selected asset
            function removeSelectedAsset(assetId) {
                selectedAssets = selectedAssets.filter(asset => asset.asset_id !== assetId);
                updateSelectedAssetsTable();
            }

            // Form submission
            document.getElementById('addCalibrationForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                if (selectedAssets.length === 0) {
                    showToast('Please select at least one asset for calibration.', 'error');
                    return;
                }

                const planningDate = document.getElementById('planning_calibration_date').value;

                if (!planningDate) {
                    showToast('Please select a schedule start date.', 'error');
                    return;
                }

                // Clear console and log what we're sending
                console.clear();
                console.log("Submitting these assets:", JSON.stringify(selectedAssets));

                // Ensure we have no duplicates in our selectedAssets array
                const uniqueAssetIds = [...new Set(selectedAssets.map(asset => asset.asset_id))];
                console.log("Original asset IDs count:", selectedAssets.length);
                console.log("Unique asset IDs count:", uniqueAssetIds.length);

                // If we detected duplicates, deduplicate the selectedAssets array
                if (uniqueAssetIds.length < selectedAssets.length) {
                    const uniqueAssets = [];
                    const seenIds = new Set();

                    // Keep only the first occurrence of each asset
                    selectedAssets.forEach(asset => {
                        if (!seenIds.has(asset.asset_id)) {
                            uniqueAssets.push(asset);
                            seenIds.add(asset.asset_id);
                        }
                    });

                    // Update the selectedAssets array
                    selectedAssets = uniqueAssets;
                    updateSelectedAssetsTable();

                    // Show a notification that we removed duplicates
                    showToast('Duplicate assets were detected and removed.', 'success');
                }

                // Prepare data for submission with unique IDs
                const formData = {
                    asset_ids: uniqueAssetIds,
                    planning_calibration_date: planningDate
                };

                // Send the request
                fetch('{{ route('calibrations.bulk.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close the modal
                        closeModal(document.getElementById('addCalibrationModal'), document.getElementById('addCalibrationModalContent'));

                        // Show toast notification first
                        showToast(data.message || 'Calibrations created successfully', 'success');

                        // Delay the redirect slightly to allow the toast to be seen
                        setTimeout(() => {
                            window.location.href = "{{ route('calibration') }}";
                        }, 1000);
                    } else {
                        // Show error message
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error creating calibrations:', error);
                    showToast('An error occurred while creating calibrations.', 'error');
                });
            });

            // Per page selection
            document.getElementById('assetPerPageSelect')?.addEventListener('change', function () {
                loadAssets(1);
            });

            // Debounce function (reuse the existing one in the file)
            function debounce(func, wait) {
                let timeout;
                return function () {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }

            // Vendor search functionality with debounce
            let allVendors = []; // Store all vendors for client-side filtering
            const vendorSearchInput = document.getElementById('vendor_search');
            const vendorIdInput = document.getElementById('vendor_id');
            const vendorResults = document.getElementById('vendor_results');

            // Initial load of vendors
            loadAllVendors();

            // Show/hide vendor results
            vendorSearchInput?.addEventListener('focus', function() {
                filterAndDisplayVendors(this.value.trim());
                vendorResults.style.display = 'block';
            });

            // Hide vendor results when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                    vendorResults.style.display = 'none';
                }
            });

            // Search vendors with debounce
            vendorSearchInput?.addEventListener('input', debounce(function() {
                const searchTerm = this.value.trim();
                filterAndDisplayVendors(searchTerm);
            }, 300));

            // Load all vendors
            function loadAllVendors() {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                vendorResults.style.display = 'block';

                // First try to get from localStorage to avoid delay
                const cachedVendors = localStorage.getItem('allVendors');
                if (cachedVendors) {
                    try {
                        allVendors = JSON.parse(cachedVendors);
                        console.log(`Loaded ${allVendors.length} vendors from cache`);

                        // Still load fresh data in the background
                        fetchAllVendors();

                        return; // Exit early with cached data
                    } catch (e) {
                        console.error('Error parsing cached vendors:', e);
                    }
                }

                // If no cache, fetch from API
                fetchAllVendors();
            }

            // Fetch all vendors with pagination
            function fetchAllVendors() {
                let page = 1;
                allVendors = []; // Reset array

                function fetchPage(page) {
                    if (page === 1) {
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                    } else {
                        // Update loading message for subsequent pages
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors (page ' + page + ')...</div>';
                    }

                    fetch(`/vendor?json=true&page=${page}&limit=100`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        let vendors = [];
                        let pagination = null;

                        // Handle different response formats
                        if (Array.isArray(data)) {
                            vendors = data;
                        } else if (data.vendors && Array.isArray(data.vendors)) {
                            vendors = data.vendors;
                            pagination = data.pagination;
                        } else if (data.data && Array.isArray(data.data)) {
                            vendors = data.data;
                            pagination = data.pagination;
                        }

                        // Add to our collection
                        allVendors = [...allVendors, ...vendors];

                        // Check if there are more pages
                        const hasNextPage = pagination && pagination.has_next;

                        if (hasNextPage) {
                            // Fetch next page
                            fetchPage(page + 1);
                        } else {
                            // We've got all vendors
                            console.log(`Loaded ${allVendors.length} vendors from API`);

                            // Cache for future use
                            try {
                                localStorage.setItem('allVendors', JSON.stringify(allVendors));
                            } catch (e) {
                                console.error('Error caching vendors:', e);
                            }

                            // If the input has a value, filter and display
                            if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                filterAndDisplayVendors(vendorSearchInput.value.trim());
                            } else {
                                vendorResults.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error(`Error fetching vendors page ${page}:`, error);
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Error loading vendors</div>';

                        // If we got some vendors, still show them
                        if (allVendors.length > 0) {
                            filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');
                        }
                    });
                }

                // Start fetching from page 1
                fetchPage(page);
            }

            // Filter and display vendors based on search term
            function filterAndDisplayVendors(searchTerm) {
                // Make sure dropdown is visible
                vendorResults.style.display = 'block';

                // Show loading message during search
                if (searchTerm && searchTerm.length > 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Searching vendors...</div>';
                }

                // If we have no vendors yet
                if (allVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                    return;
                }

                // Filter vendors
                let filteredVendors = allVendors;
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    filteredVendors = allVendors.filter(vendor =>
                        vendor.vendor_name?.toLowerCase().includes(term)
                    );
                }

                // Sort by relevance if we have a search term
                if (searchTerm) {
                    filteredVendors.sort((a, b) => {
                        // Exact matches first
                        if (a.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return -1;
                        if (b.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return 1;

                        // Then starts-with matches
                        const aStarts = a.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        const bStarts = b.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        if (aStarts && !bStarts) return -1;
                        if (bStarts && !aStarts) return 1;

                        // Then alphabetical
                        return a.vendor_name.localeCompare(b.vendor_name);
                    });
                }

                // Limit to first 20 for performance
                const displayVendors = filteredVendors.slice(0, 20);

                // Update DOM with animation delay
                vendorResults.innerHTML = '';

                if (displayVendors.length === 0) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">No vendors found</div>';
                    return;
                }

                // Add vendor items with staggered animation
                displayVendors.forEach((vendor, index) => {
                    const div = document.createElement('div');
                    div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                    div.textContent = vendor.vendor_name;
                    div.setAttribute('data-id', vendor.vendor_id);
                    div.style.animationDelay = `${index * 30}ms`; // Staggered animation

                    div.addEventListener('click', function() {
                        vendorIdInput.value = this.getAttribute('data-id');
                        vendorSearchInput.value = this.textContent;
                        vendorResults.style.display = 'none';
                    });

                    vendorResults.appendChild(div);
                });

                // Show count if limited
                if (filteredVendors.length > 20) {
                    const countDiv = document.createElement('div');
                    countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                    countDiv.textContent = `Showing 20 of ${filteredVendors.length} vendors`;
                    vendorResults.appendChild(countDiv);
                }
            }
        });
    </script>
    @endpush
@endsection
