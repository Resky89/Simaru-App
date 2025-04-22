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

                    <!-- Button Export PDF -->
                    <button id="exportBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="text-base">Export PDF</span>
                    </button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportBtn = document.getElementById('exportBtn');
        const searchInput = document.getElementById('searchInput');
        const sortOrder = document.getElementById('sortOrder');
        const statusFilter = document.getElementById('statusFilter');
        const perPageSelect = document.getElementById('perPageSelect');

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

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            window.location.href = url.toString();
        }

        // Add event listeners
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
            const exportUrl = "{{ route('report.complain.export.pdf') }}?" + searchParams.toString();

            // Redirect to the export URL
            window.open(exportUrl, '_blank');
        });
    });

    // Function to view complaint details
    function viewComplaintDetails(id) {
        // Redirect to the complaint detail page
        window.location.href = "{{ route('complaint.detail', '') }}/" + id;
    }
</script>
@endpush
@endsection
