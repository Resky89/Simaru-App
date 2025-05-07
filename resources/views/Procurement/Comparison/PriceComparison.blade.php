@extends('Layout.app')

@section('title', 'Price Comparison')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Price Comparison Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PRICE COMPARISON</h1>

                        <!-- Add Comparison Button -->
                        <a href="{{ route('procurement.form-comparison') }}" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Create</span>
                        </a>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Search by title, quotation number..."
                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <select id="statusFilter"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Status</option>
                            <option value="">All Status</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Sort Order</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="title_asc">Title (A-Z)</option>
                            <option value="title_desc">Title (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Price Comparison Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation Number</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Request ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Title</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation by</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation Date</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comparisons as $comparison)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['comparison_code'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['procurement_code'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['title'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['created_by']['name'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['created_at'] ? date('d M Y', strtotime($comparison['created_at'])) : 'N/A' }}</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        @if($comparison['status'] ?? '' == 'Submitted')
                                        <button class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-comparison-btn"
                                                data-id="{{ $comparison['comparison_id'] ?? '' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @else
                                        <span class="w-5 h-5 inline-block"></span>
                                        @endif

                                        @if($comparison['status'] ?? '' == 'Submitted')
                                        <button class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-comparison-btn"
                                                data-id="{{ $comparison['comparison_id'] ?? '' }}"
                                                data-title="{{ $comparison['title'] ?? '' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @else
                                        <span class="w-5 h-5 inline-block"></span>
                                        @endif

                                        <a href="{{ route('procurement.detail-comparison', ['id' => $comparison['comparison_id'] ?? '']) }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-3 text-center text-gray-500">No price comparisons found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($pagination) && is_array($pagination))
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) - 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                            <div class="flex gap-1">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $totalPages = $pagination['total_pages'] ?? 1;
                                    $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                                    $endPage = min($totalPages, max(5, $currentPage + 2));
                                @endphp

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                <button class="w-8 h-8 {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268]' }} rounded text-sm hover:bg-gray-50 {{ $i == $currentPage ? '' : 'hover:bg-gray-100' }}"
                                       onclick="changePage({{ $i }})">
                                    {{ $i }}
                                </button>
                                @endfor
                        </div>

                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        @else
                            <!-- Default pagination when no data -->
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                                Prev
                    </button>
                            <button class="w-8 h-8 bg-[#213268] text-white rounded text-sm">1</button>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($pagination) && is_array($pagination))
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $perPage = $pagination['limit'] ?? 10;
                                    $total = $pagination['total_items'] ?? count($comparisons);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($comparisons) }} of {{ count($comparisons) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['limit']) && $pagination['limit'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($pagination['limit']) && $pagination['limit'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($pagination['limit']) && $pagination['limit'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success and Error Notifications -->
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

@if(session('error'))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('errorNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortOrder = document.getElementById('sortOrder');

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const statusValue = statusFilter?.value || '';
            const sortValue = sortOrder?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'status', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (statusValue) url.searchParams.set('status', statusValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            // Reset to page 1 when filters change
            url.searchParams.set('page', 1);

            // Navigate to the new URL
            window.location.href = url.toString();
        }

        // Add event listeners with debounce for search
        let searchTimeout;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Add event listeners for select filters
        statusFilter?.addEventListener('change', applyFilters);
        sortOrder?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (statusFilter) {
            const statusValue = urlParams.get('status');
            if (statusValue) {
                statusFilter.value = statusValue;
            }
        }
        if (sortOrder) {
            const sortValue = urlParams.get('sort');
            if (sortValue) {
                sortOrder.value = sortValue;
            }
        }

        // Pagination functions
        window.changePage = function(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.href = '{{ route("procurement.price-comparison") }}?' + urlParams.toString();
        };

        window.changePerPage = function(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = '{{ route("procurement.price-comparison") }}?' + urlParams.toString();
        };

        // Add click events for Edit buttons
        const editBtns = document.querySelectorAll('.edit-comparison-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const comparisonId = btn.getAttribute('data-id');
                if (comparisonId) {
                    window.location.href = '{{ route("procurement.form-comparison") }}?id=' + comparisonId;
                }
            });
        });

        // Add click events for Delete buttons
        const deleteBtns = document.querySelectorAll('.delete-comparison-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const comparisonId = btn.getAttribute('data-id');
                const comparisonTitle = btn.getAttribute('data-title');
                if (comparisonId) {
                    // Implement delete confirmation dialog here
                    if (confirm(`Are you sure you want to delete the price comparison: ${comparisonTitle}?`)) {
                        // Send delete request
                        fetch(`/procurement/price-comparison/${comparisonId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Failed to delete: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting');
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
