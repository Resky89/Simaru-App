@extends('Layout.app')

@section('title', 'Brands Management')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Brand Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">Brands Management</h1>

                    <!-- Add Brand Button -->
                    <button id="addBrandBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add New Brand</span>
                    </button>
                </div>

                <!-- Brand Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Brand Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $brand['brand_id'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $brand['brand_name'] }}</td>
                                    <td class="p-3 border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-brand-btn"
                                                    data-brand-id="{{ $brand['brand_id'] }}"
                                                    data-brand-name="{{ $brand['brand_name'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-brand-btn"
                                                    data-brand-id="{{ $brand['brand_id'] }}"
                                                    data-brand-name="{{ $brand['brand_name'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-3 text-xs text-center border-t border-[#EEF1F4]">No brands found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($brands_pagination))
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($brands_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($brands_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($brands_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        <div class="flex gap-2">
                            @php
                                $currentPage = $brands_pagination['current_page'] ?? 1;
                                $lastPage = $brands_pagination['last_page'] ?? 1;
                            @endphp

                            @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                   class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($brands_pagination['current_page'] ?? 1) >= ($brands_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($brands_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($brands_pagination['current_page'] ?? 1) >= ($brands_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                                @php
                                    $currentPage = $brands_pagination['current_page'] ?? 1;
                                    $perPage = $brands_pagination['per_page'] ?? 10;
                                    $total = $brands_pagination['total'] ?? count($brands ?? []);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                </div>
                @endif
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

<!-- Modal Add Brand -->
<div id="addBrandModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="addBrandModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD BRAND</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                <form action="{{ route('brands.store') }}" method="POST">
                    @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Brand Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Brand Name</label>
                                <input type="text" name="brand_name" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Save
                            </button>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Brand -->
<div id="editBrandModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editBrandModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT BRAND</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                <form id="editBrandForm" method="POST">
                    @csrf
                    @method('PUT')
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Brand Name</label>
                                <input type="text" id="edit_brand_name" name="brand_name" required
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Brand Modal -->
<div id="deleteBrandModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteBrandModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE BRAND</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Are you sure you want to delete the brand: <span id="delete_brand_name" class="font-bold"></span>? This action cannot be undone.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Cancel
                            </button>
                            <form id="deleteBrandForm" method="POST" class="w-1/2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle toast notifications
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

    // Page navigation functions
        window.changePage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = url.toString();
    }

    // Modal functionality
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

        // Handle Edit Brand button click
        document.querySelectorAll('.edit-brand-btn').forEach(button => {
            button.addEventListener('click', function() {
                const brandId = this.getAttribute('data-brand-id');
                const brandName = this.getAttribute('data-brand-name');

                document.getElementById('edit_brand_name').value = brandName;
                document.getElementById('editBrandForm').action = `/brands/${brandId}`;
                console.log('Edit form action set to:', `/brands/${brandId}`);

                const modal = document.getElementById('editBrandModal');
                const content = document.getElementById('editBrandModalContent');
                if (modal && content) openModal(modal, content);
            });
        });

        // Handle Delete Brand button click
        document.querySelectorAll('.delete-brand-btn').forEach(button => {
            button.addEventListener('click', function() {
                const brandId = this.getAttribute('data-brand-id');
                const brandName = this.getAttribute('data-brand-name');

                document.getElementById('delete_brand_name').textContent = brandName;
                document.getElementById('deleteBrandForm').action = `/brands/${brandId}`;
                console.log('Delete form action set to:', `/brands/${brandId}`);

                const modal = document.getElementById('deleteBrandModal');
                const content = document.getElementById('deleteBrandModalContent');
                if (modal && content) openModal(modal, content);
            });
        });

        // Handle Add Brand button click
        document.getElementById('addBrandBtn').addEventListener('click', function() {
            const modal = document.getElementById('addBrandModal');
            const content = document.getElementById('addBrandModalContent');
            if (modal && content) openModal(modal, content);
        });

        // Close buttons
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        // Close modal when clicking on background or overlay
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                // Check if the click is directly on the modal's overlay area
                if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                    e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                    const content = this.querySelector('[id$="ModalContent"]');
                    closeModal(this, content);
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });

        // Function to show toast notifications
        window.showToast = function(message, type = 'success') {
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
    });
</script>
@endpush
