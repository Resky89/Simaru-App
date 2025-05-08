@extends('Layout.app')

@section('title', 'Complaint & Repair')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Complaint & Repair Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">COMPLAINT & REPAIR</h1>

                    <div class="flex gap-3">
                        <!-- Create Complaint Button -->
                        <button id="createComplaintBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Create Complaint</span>
                        </button>

                        <!-- Button Export PDF -->
                        <button id="exportBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span class="text-base">Export PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Search by asset name or description..." value="{{ $search ?? '' }}"
                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="newest" {{ ($sort ?? 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ ($sort ?? 'newest') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                        <select id="statusFilter"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" {{ ($status ?? '') == '' ? 'selected' : '' }}>All Status</option>
                            <option value="new" {{ ($status ?? '') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="in_progress" {{ ($status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                @if (isset($error))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ $error }}</p>
                </div>
                @endif

                <!-- Complaint & Repair Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Complaint Date</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Finished Date</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Reporter</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="complaintsTableBody">
                            @forelse($complaints ?? [] as $complaint)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $complaint['asset_name'] ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        {{ $complaint['description'] ?? '-' }}
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @php
                                            $statusClass = '';
                                            $status = $complaint['status'] ?? '';

                                            if ($status == 'approved' || $status == 'completed') {
                                                $statusClass = 'bg-green-100 text-green-800';
                                            } elseif ($status == 'pending' || $status == 'new') {
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                            } elseif ($status == 'rejected') {
                                                $statusClass = 'bg-red-100 text-red-800';
                                            } elseif ($status == 'in_progress') {
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                            } else {
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $status ?: 'Unknown')) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        {{ isset($complaint['complaint_date']) ? date('d M Y', strtotime($complaint['complaint_date'])) : '-' }}
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        {{ isset($complaint['finished_date']) && $complaint['finished_date'] ? date('d M Y', strtotime($complaint['finished_date'])) : '-' }}
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        ID: {{ $complaint['reporter_number'] ?? '-' }}
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="viewComplaintDetails({{ $complaint['id'] }})"
                                                class="text-[#3D3D3D] hover:text-[#213268]"
                                                title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button
                                                class="text-[#3D3D3D] hover:text-[#213268] repair-complaint-btn"
                                                data-id="{{ $complaint['id'] }}"
                                                data-asset="{{ $complaint['asset_name'] ?? 'Unknown' }}"
                                                title="Perform Repair">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                            </button>
                                            <button
                                                class="text-[#3D3D3D] hover:text-red-500 delete-complaint-btn"
                                                data-id="{{ $complaint['id'] }}"
                                                data-name="{{ $complaint['asset_name'] ?? 'Unknown' }}"
                                                title="Delete Complaint">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No complaints found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($pagination) && $pagination)
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
                            Showing {{ ($pagination['current_page'] - 1) * $pagination['limit'] + 1 }}
                            to {{ min($pagination['current_page'] * $pagination['limit'], $pagination['total_items']) }}
                            of {{ $pagination['total_items'] }} entries
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
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Create Complaint Modal -->
<div id="createComplaintModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="createComplaintModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">CREATE COMPLAINT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Error messages container -->
                <div id="errorMessages" class="px-6 pt-4">
                    @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <p class="font-bold">Validation errors:</p>
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Form -->
                <form id="complaintForm" action="{{ route('complaint.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="handle_ajax" value="0">
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Complaint Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Complaint Information</h3>

                            <!-- Asset Selection -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Asset*</label>
                                <div class="relative">
                                    <input type="text" id="assetSearch"
                                        placeholder="Search for an asset..."
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20"
                                    />
                                    <input type="hidden" id="assetId" name="asset_id" required />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div id="assetDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg overflow-y-auto max-h-60 hidden">
                                        <div class="p-2" id="assetDropdownContent">
                                            <!-- Options will be populated dynamically -->
                                        </div>
                                        <div id="assetLoadingIndicator" class="p-2 text-center text-gray-500 hidden">
                                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="mt-1">Loading...</p>
                                        </div>
                                        <div id="assetNoResults" class="p-2 text-center text-gray-500 hidden">
                                            No assets found
                                        </div>
                                    </div>
                                </div>
                                <div id="selectedAssetInfo" class="mt-2 p-2 bg-gray-100 rounded-lg hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium" id="selectedAssetName"></p>
                                            <p class="text-sm text-gray-500" id="selectedAssetId"></p>
                                        </div>
                                        <button type="button" id="clearAssetSelection" class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description*</label>
                                <textarea id="description" name="description" rows="4" required
                                    class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 resize-none"
                                    placeholder="Describe the issue..."></textarea>
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Image*</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Image preview -->
                                    <div id="imagePreview" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img id="previewImg" src="#" alt="Preview" class="w-full h-auto max-h-64 object-contain mx-auto rounded">
                                            <button type="button" id="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: jpg, jpeg, png (Max file size: 5MB)</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input id="imageFile" name="image_file" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" required />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Create Complaint
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Complaint Confirmation Modal -->
<div id="deleteComplaintModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteComplaintModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Delete Complaint</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <form id="deleteComplaintForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteComplaintId" name="complaint_id">
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Are you sure you want to delete this complaint? This action cannot be undone.</p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button"
                                    class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
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

<!-- Repair Complaint Modal -->
<div id="repairComplaintModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="repairComplaintModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">PERFORM REPAIR</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Error messages container -->
                <div id="repairErrorMessages" class="px-6 pt-4"></div>

                <!-- Form -->
                <form id="repairForm" action="{{ route('complaint.repair.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="complaint_id" id="repairComplaintId">
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Repair Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Repair Information</h3>

                            <!-- Asset Name Display -->
                            <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                                <p class="text-sm text-gray-500">Repairing Asset:</p>
                                <p class="text-base font-medium" id="repairAssetName"></p>
                            </div>

                            <!-- Repair Description -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Repair Description*</label>
                                <textarea id="repairDescription" name="repair_description" rows="3" required
                                    class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 resize-none"
                                    placeholder="Describe the repair work..."></textarea>
                            </div>

                             <!-- Final Result -->
                             <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Final Result*</label>
                                <select id="finalResult" name="final_result" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20">
                                    <option value="" disabled selected>Select a final result</option>
                                    <option value="Good">Good</option>
                                    <option value="Slightly Damage">Slightly Damage</option>
                                    <option value="Heavy Damage">Heavy Damage</option>
                                    <option value="Waiting for Part">Waiting for Part</option>
                                </select>
                            </div>

                            <!-- Repair Cost -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Repair Cost*</label>
                                <input type="number" id="repairCost" name="repair_cost" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20"
                                    placeholder="Cost in IDR">
                            </div>

                            <!-- Parts Replaced -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Parts Replaced*</label>
                                <input type="text" id="partsReplaced" name="parts_replaced" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20"
                                    placeholder="List of replaced parts">
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Repair Image*</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Image preview -->
                                    <div id="repairImagePreview" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img id="repairPreviewImg" src="#" alt="Preview" class="w-full h-auto max-h-64 object-contain mx-auto rounded">
                                            <button type="button" id="removeRepairImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: jpg, jpeg, png (Max file size: 5MB)</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input id="repairImageFile" name="file" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" required />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Submit Repair
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successNotification" class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('successNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

<script>
    // Define showToast function first
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 flex items-center';

        if (type === 'success') {
            toast.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
        } else {
            toast.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
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
                <p class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</p>
                <p>${message}</p>
            </div>
            <span class="ml-4 cursor-pointer" onclick="this.parentElement.remove()">×</span>
        `;

        document.body.appendChild(toast);

        // Auto-remove the toast after 5 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ===== VARIABLE DECLARATIONS =====
        // DOM Elements
        const imageFile = document.getElementById('imageFile');
        const previewImg = document.getElementById('previewImg');
        const imagePreview = document.getElementById('imagePreview');
        const removeImage = document.getElementById('removeImage');
        const complaintForm = document.getElementById('complaintForm');
        const errorMsgDiv = document.getElementById('errorMessages');
        const createComplaintBtn = document.getElementById('createComplaintBtn');
        const createComplaintModal = document.getElementById('createComplaintModal');
        const createComplaintModalContent = document.getElementById('createComplaintModalContent');
        const closeModalBtns = document.querySelectorAll('.close-modal');
        const exportBtn = document.getElementById('exportBtn');
        const searchInput = document.getElementById('searchInput');
        const sortOrder = document.getElementById('sortOrder');
        const statusFilter = document.getElementById('statusFilter');
        const perPageSelect = document.getElementById('perPageSelect');
        const assetSearch = document.getElementById('assetSearch');
        const assetDropdown = document.getElementById('assetDropdown');
        const assetDropdownContent = document.getElementById('assetDropdownContent');
        const assetLoadingIndicator = document.getElementById('assetLoadingIndicator');
        const assetNoResults = document.getElementById('assetNoResults');
        const assetId = document.getElementById('assetId');
        const selectedAssetInfo = document.getElementById('selectedAssetInfo');
        const selectedAssetName = document.getElementById('selectedAssetName');
        const selectedAssetId = document.getElementById('selectedAssetId');
        const clearAssetSelection = document.getElementById('clearAssetSelection');

        // Check for flash messages from session and show toast notifications
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // ===== UTILITY FUNCTIONS =====
        // Modal functions
        function openModal(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }

        function closeModal(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Debounce function to limit how often search is triggered
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Function to apply filters
        function applyFilters() {
            const searchTerm = searchInput.value;
            const sort = sortOrder.value;
            const status = statusFilter.value;
            const limit = perPageSelect?.value || 10;

            const url = new URL(window.location.href);

            // Set search parameter
            if (searchTerm) url.searchParams.set('search', searchTerm);
            else url.searchParams.delete('search');

            // Set sort parameter
            if (sort) url.searchParams.set('sort', sort);
            else url.searchParams.delete('sort');

            // Set status parameter
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');

            // Set limit parameter
            url.searchParams.set('limit', limit);

            // Reset to first page when filters change
            url.searchParams.set('page', 1);

            // Redirect to new URL with filters
            window.location.href = url.toString();
        }

        imageFile?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }

                reader.readAsDataURL(file);
            }
        });

        removeImage?.addEventListener('click', function() {
            imageFile.value = '';
            imagePreview.classList.add('hidden');
            previewImg.src = '#';
        });

        // Modal Controls
        createComplaintBtn?.addEventListener('click', function() {
            openModal(createComplaintModal, createComplaintModalContent);

            // Clear form and error messages
            complaintForm?.reset();
            if (errorMsgDiv) errorMsgDiv.innerHTML = '';

            // Reset image preview
            if (imagePreview) {
                imagePreview.classList.add('hidden');
            }
        });

        closeModalBtns?.forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        createComplaintModal?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(createComplaintModal, createComplaintModalContent);
            }
        });

        // Search and Filtering
        searchInput?.addEventListener('input', debounce(function() {
            applyFilters();
        }, 500));

        sortOrder?.addEventListener('change', function() {
            applyFilters();
        });

        statusFilter?.addEventListener('change', function() {
            applyFilters();
        });

        // Export PDF functionality
        exportBtn?.addEventListener('click', () => {
            // Get current URL parameters
            const url = new URL(window.location.href);
            const searchParams = url.searchParams;

            // Create the PDF export URL with the same parameters
            const exportUrl = "{{ route('complaint.export.pdf') }}?" + searchParams.toString();

            // Open in a new window/tab, not replacing the current one
            window.open(exportUrl, '_blank', 'noopener,noreferrer');
        });

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            window.location.href = url.toString();
        }

        // ===== ASSET SEARCH FUNCTIONALITY WITH DEBOUNCE =====
        const assets = @json($assets ?? []);
        let assetSearchTimeout;

        // Log available assets data to console for debugging
        console.log('Assets loaded:', assets.length);
        if (assets.length > 0) {
            console.log('First asset sample:', assets[0]);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (assetSearch && assetDropdown && !assetSearch.contains(e.target) && !assetDropdown.contains(e.target)) {
                assetDropdown.classList.add('hidden');
            }
        });

        // Open dropdown when focusing on search input
        assetSearch?.addEventListener('focus', function() {
            // Only show dropdown if we haven't selected an asset yet
            if (!assetId.value) {
                // Make sure we have assets data before showing dropdown
                if (assets && assets.length > 0) {
                    displayFilteredAssets(assets, '');
                    assetDropdown.classList.remove('hidden');
                } else {
                    // No assets available
                    assetNoResults.classList.remove('hidden');
                    assetDropdown.classList.remove('hidden');
                }
            }
        });

        // Handle asset search with debounce
        assetSearch?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            // Show loading indicator and dropdown
            assetLoadingIndicator.classList.remove('hidden');
            assetNoResults.classList.add('hidden');
            assetDropdownContent.innerHTML = '';
            assetDropdown.classList.remove('hidden');

            // Clear any existing timeout
            clearTimeout(assetSearchTimeout);

            // Set new timeout for debounce (300ms)
            assetSearchTimeout = setTimeout(function() {
                // Filter assets client-side
                filterAssets(searchTerm);
            }, 300);
        });

        // Function to filter assets based on search term
        function filterAssets(searchTerm) {
            assetLoadingIndicator.classList.add('hidden');

            if (!assets || assets.length === 0) {
                assetNoResults.classList.remove('hidden');
                return;
            }

            // Filter assets by name, code or ID
            let filteredAssets = assets;
            if (searchTerm) {
                filteredAssets = assets.filter(asset =>
                    (asset.asset_name && asset.asset_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                    (asset.asset_master_name && asset.asset_master_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                    (asset.asset_code && asset.asset_code.toLowerCase().includes(searchTerm.toLowerCase())) ||
                    (asset.asset_id && asset.asset_id.toString().includes(searchTerm))
                );
            }

            displayFilteredAssets(filteredAssets, searchTerm);
        }

        // Function to display filtered assets in dropdown
        function displayFilteredAssets(filteredAssets, searchTerm) {
            assetDropdownContent.innerHTML = '';

            if (!filteredAssets || filteredAssets.length === 0) {
                assetNoResults.classList.remove('hidden');
                return;
            }

            assetNoResults.classList.add('hidden');
            assetLoadingIndicator.classList.add('hidden');

            // Limit to first 100 results for performance
            const assetsToShow = filteredAssets.slice(0, 100);

            assetsToShow.forEach(asset => {
                const div = document.createElement('div');
                div.className = 'p-2 hover:bg-gray-100 cursor-pointer rounded transition-colors';
                div.innerHTML = `
                    <div class="font-medium">${asset.asset_master_name || asset.asset_name || 'Unknown Asset'}</div>
                    <div class="text-xs text-gray-500">Code: ${asset.asset_code || 'N/A'}</div>
                `;

                div.addEventListener('click', function() {
                    selectAsset(asset);
                });

                assetDropdownContent.appendChild(div);
            });
        }

        // Function to select an asset
        function selectAsset(asset) {
            assetId.value = asset.asset_id;
            assetSearch.value = asset.asset_master_name || asset.asset_name;
            assetDropdown.classList.add('hidden');

            // Show selected asset info
            selectedAssetName.textContent = asset.asset_master_name || asset.asset_name;
            selectedAssetId.textContent = `Code: ${asset.asset_code || 'N/A'}`;
            selectedAssetInfo.classList.remove('hidden');
        }

        // Clear asset selection
        clearAssetSelection?.addEventListener('click', function() {
            assetId.value = '';
            assetSearch.value = '';
            selectedAssetInfo.classList.add('hidden');
        });

        // ===== FORM SUBMISSION =====
        complaintForm?.addEventListener('submit', function(e) {
            // Basic client-side validation
            const formData = new FormData(complaintForm);
            let isValid = true;
            let errorMessage = '';

            // Basic validation for required fields
            if (!formData.get('asset_id')) {
                isValid = false;
                errorMessage = 'Asset is required';
            }

            if (!formData.get('description').trim()) {
                isValid = false;
                errorMessage = 'Description is required';
            }

            // Check for image file
            if (!formData.get('image_file') || formData.get('image_file').size === 0) {
                isValid = false;
                errorMessage = 'Image is required';
            }

            // If validation fails, prevent form submission and show error
            if (!isValid) {
                e.preventDefault();
                errorMsgDiv.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <p class="font-bold">Validation Error</p>
                        <p>${errorMessage}</p>
                    </div>
                `;
                errorMsgDiv.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            // If validation passes, form will submit normally
        });

        // Repair form submission validation
        const repairForm = document.getElementById('repairForm');
        const repairErrorMsgDiv = document.getElementById('repairErrorMessages');

        repairForm?.addEventListener('submit', function(e) {
            // Basic client-side validation
            const formData = new FormData(repairForm);
            let isValid = true;
            let errorMessage = '';

            // Basic validation for required fields
            if (!formData.get('complaint_id')) {
                isValid = false;
                errorMessage = 'Complaint ID is required';
            }

            if (!formData.get('repair_description').trim()) {
                isValid = false;
                errorMessage = 'Repair description is required';
            }

            if (!formData.get('final_result').trim()) {
                isValid = false;
                errorMessage = 'Final result is required';
            }

            if (!formData.get('repair_cost')) {
                isValid = false;
                errorMessage = 'Repair cost is required';
            }

            if (!formData.get('parts_replaced').trim()) {
                isValid = false;
                errorMessage = 'Parts replaced is required';
            }

            // Check for image file
            if (!formData.get('file') || formData.get('file').size === 0) {
                isValid = false;
                errorMessage = 'Repair image is required';
            }

            // If validation fails, prevent form submission and show error
            if (!isValid) {
                e.preventDefault();
                repairErrorMsgDiv.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <p class="font-bold">Validation Error</p>
                        <p>${errorMessage}</p>
                    </div>
                `;
                repairErrorMsgDiv.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            // If validation passes, form will submit normally
        });

        // Delete complaint functionality
        const deleteComplaintModal = document.getElementById('deleteComplaintModal');
        const deleteComplaintModalContent = document.getElementById('deleteComplaintModalContent');
        const deleteComplaintForm = document.getElementById('deleteComplaintForm');
        const deleteComplaintId = document.getElementById('deleteComplaintId');

        // Repair complaint functionality
        const repairComplaintModal = document.getElementById('repairComplaintModal');
        const repairComplaintModalContent = document.getElementById('repairComplaintModalContent');
        const repairComplaintId = document.getElementById('repairComplaintId');
        const repairAssetName = document.getElementById('repairAssetName');
        const repairImageFile = document.getElementById('repairImageFile');
        const repairPreviewImg = document.getElementById('repairPreviewImg');
        const repairImagePreview = document.getElementById('repairImagePreview');
        const removeRepairImage = document.getElementById('removeRepairImage');

        // Image preview for repair
        repairImageFile?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    repairPreviewImg.src = e.target.result;
                    repairImagePreview.classList.remove('hidden');
                }

                reader.readAsDataURL(file);
            }
        });

        removeRepairImage?.addEventListener('click', function() {
            repairImageFile.value = '';
            repairImagePreview.classList.add('hidden');
            repairPreviewImg.src = '#';
        });

        // Delete button click handlers
        document.querySelectorAll('.delete-complaint-btn').forEach(button => {
            button.addEventListener('click', () => {
                const complaintId = button.getAttribute('data-id');
                deleteComplaintForm.action = `{{ url('complaint/destroy') }}/${complaintId}`;
                deleteComplaintId.value = complaintId;

                // Open delete modal
                openModal(deleteComplaintModal, deleteComplaintModalContent);
            });
        });

        // Repair button click handlers
        document.querySelectorAll('.repair-complaint-btn').forEach(button => {
            button.addEventListener('click', () => {
                const complaintId = button.getAttribute('data-id');
                const assetName = button.getAttribute('data-asset');

                // Set form data
                repairComplaintId.value = complaintId;
                repairAssetName.textContent = assetName;

                // Reset form and error messages
                repairForm?.reset();
                if (repairErrorMsgDiv) repairErrorMsgDiv.innerHTML = '';

                // Reset image preview
                if (repairImagePreview) {
                    repairImagePreview.classList.add('hidden');
                }

                // Open repair modal
                openModal(repairComplaintModal, repairComplaintModalContent);
            });
        });

        // Close modal when clicking outside
        deleteComplaintModal?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(deleteComplaintModal, deleteComplaintModalContent);
            }
        });

        // Close repair modal when clicking outside
        repairComplaintModal?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(repairComplaintModal, repairComplaintModalContent);
            }
        });
    });

    // Function to view complaint details - defined globally
    function viewComplaintDetails(id) {
        // Redirect to the complaint detail page
        window.location.href = "{{ url('complaint/detail') }}/" + id;
    }
</script>
@endsection

