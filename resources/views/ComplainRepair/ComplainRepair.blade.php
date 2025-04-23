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
                            <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                                            <span class="text-gray-500">ID: {{ $complaint['asset_id'] ?? '-' }}</span>
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
                                            } elseif ($status == 'pending') {
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
                                                class="p-1 text-[#213268] hover:bg-gray-100 rounded-full transition-all duration-200"
                                                title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                <div id="errorMessages" class="px-6 pt-4"></div>

                <!-- Form -->
                <form id="complaintForm" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Complaint Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Complaint Information</h3>

                            <!-- Asset Selection -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Asset*</label>
                                <select id="assetId" name="asset_id" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    <option value="" selected disabled>Select an asset</option>
                                    @foreach($assets ?? [] as $asset)
                                        <option value="{{ $asset['asset_id'] }}">{{ $asset['asset_name'] }} (ID: {{ $asset['asset_id'] }})</option>
                                    @endforeach
                                </select>
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
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative flex flex-col items-center justify-center">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-blue-600">browse</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png (Max file size: 5MB)</p>
                                    </div>
                                    <input id="imageFile" name="image_file" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" required />
                                    <!-- Preview image container -->
                                    <div id="imagePreview" class="mt-4 w-full hidden">
                                        <div class="relative">
                                            <img id="previewImg" src="#" alt="Preview" class="max-h-40 mx-auto rounded-lg">
                                            <button type="button" id="removeImage" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
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
@endsection

@push('scripts')
<script>
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
        const assetIdSelect = document.getElementById('assetId');

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

        // ===== DATA LOADING FUNCTIONS =====
        function showNotification(title, message, type = 'info') {
            // Check if notification container exists, if not create it
            let notificationContainer = document.getElementById('notification-container');

            if (!notificationContainer) {
                notificationContainer = document.createElement('div');
                notificationContainer.id = 'notification-container';
                notificationContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-md';
                document.body.appendChild(notificationContainer);
            }

            // Create notification element
            const notification = document.createElement('div');

            // Set classes based on notification type
            let bgColor = 'bg-blue-500';
            if (type === 'success') bgColor = 'bg-green-500';
            if (type === 'error') bgColor = 'bg-red-500';
            if (type === 'warning') bgColor = 'bg-yellow-500';

            notification.className = `${bgColor} text-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out transform translate-x-0`;

            // Set notification content
            notification.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${type === 'success'
                                ? `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                  </svg>`
                                : `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                  </svg>`
                            }
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium">${title}</p>
                            <p class="mt-1 text-sm">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="inline-flex text-white focus:outline-none focus:text-gray-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Add close functionality
            const closeBtn = notification.querySelector('button');
            closeBtn.addEventListener('click', () => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            });

            // Add to container
            notificationContainer.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
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

            // Redirect to the export URL
            window.open(exportUrl, '_blank');
        });

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            window.location.href = url.toString();
        }

        // ===== FORM SUBMISSION =====
        complaintForm?.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(complaintForm);

            // Validate form
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

            // If validation fails, show error and exit
            if (!isValid) {
                errorMsgDiv.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <p class="font-bold">Validation Error</p>
                        <p>${errorMessage}</p>
                    </div>
                `;
                errorMsgDiv.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            // Show loading state
            const submitBtn = complaintForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            `;

            // Submit form data
            fetch('{{ route('complaint.create') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                if (data.status) {
                    // Success - close modal and reload page
                    closeModal(createComplaintModal, createComplaintModalContent);

                    // Show success message
                    showNotification('Success', data.message, 'success');

                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show validation errors
                    if (data.errors) {
                        let errorHtml = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">';
                        errorHtml += '<p class="font-bold">Validation errors:</p><ul class="list-disc pl-5">';

                        Object.keys(data.errors).forEach(field => {
                            data.errors[field].forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                        });

                        errorHtml += '</ul></div>';
                        errorMsgDiv.innerHTML = errorHtml;
                    } else {
                        // Show general error message
                        errorMsgDiv.innerHTML = `
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                                <p class="font-bold">Error</p>
                                <p>${data.message || 'An error occurred while creating the complaint.'}</p>
                            </div>
                        `;
                    }

                    // Scroll to error messages
                    errorMsgDiv.scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                // Show error message
                errorMsgDiv.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <p class="font-bold">Error</p>
                        <p>An unexpected error occurred. Please try again.</p>
                    </div>
                `;
                console.error('Error submitting complaint:', error);
            });
        });

        // ===== GLOBAL FUNCTIONS =====
        // Function to view complaint details - defined globally
        function viewComplaintDetails(id) {
            // Redirect to the complaint detail page
            window.location.href = "{{ route('complaint.detail', '') }}/" + id;
        }
    });
</script>

<script>
    // ===== GLOBAL FUNCTIONS =====
    // Function to view complaint details - defined globally
    function viewComplaintDetails(id) {
        // Redirect to the complaint detail page
        window.location.href = "{{ route('complaint.detail', '') }}/" + id;
    }
</script>
@endpush

