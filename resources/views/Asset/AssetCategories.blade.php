@extends('Layout.app')

@section('title', 'Asset Categories')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Sub Categories Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">SUB CATEGORIES</h1>

                    <!-- Button Add Sub Categories -->
                    <button id="addSubCategoryBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Sub Categories</span>
                    </button>
                </div>

                <!-- Sub Categories Table will go here -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">Sub Category Id</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[25%]">Asset Type</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Sub Categories</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $subcategory)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $subcategory['subcategory_id'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if($subcategory['asset_type'] == 'medical')
                                            Medical
                                        @elseif($subcategory['asset_type'] == 'non_medical')
                                            Non Medical
                                        @else
                                            {{ $subcategory['asset_type'] }}
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $subcategory['subcategory_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-subcategory-btn"
                                                    data-subcategory-id="{{ $subcategory['subcategory_id'] }}"
                                                    data-asset-type="{{ $subcategory['asset_type'] }}"
                                                    data-subcategory-name="{{ $subcategory['subcategory_name'] }}"
                                                    data-description="{{ $subcategory['description'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-subcategory-btn"
                                                    data-subcategory-id="{{ $subcategory['subcategory_id'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-3 text-xs text-center border-t border-[#EEF1F4]">No subcategories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($pagination) && is_array($pagination))
                            @php
                                $currentPage = $pagination['current_page'] ?? 1;
                                $lastPage = $pagination['last_page'] ?? $currentPage;
                            @endphp

                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ $currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                onclick="{{ $currentPage > 1 ? 'changePage('.($currentPage - 1).')' : 'void(0)' }}"
                                {{ $currentPage <= 1 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <div class="flex gap-2">
                                @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                    <button onclick="changePage({{ $i }})"
                                            class="w-8 h-8 flex items-center justify-center {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268] hover:bg-gray-50' }} rounded text-sm">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ $currentPage >= $lastPage ? 'opacity-50 cursor-not-allowed' : '' }}"
                                onclick="{{ $currentPage < $lastPage ? 'changePage('.($currentPage + 1).')' : 'void(0)' }}"
                                {{ $currentPage >= $lastPage ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <div class="flex gap-2">
                                <button class="w-8 h-8 flex items-center justify-center bg-[#213268] rounded text-white text-sm">1</button>
                            </div>
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
                                    $perPage = $pagination['per_page'] ?? 10;
                                    $total = $pagination['total'] ?? count($subcategories);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($subcategories) }} of {{ count($subcategories) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['per_page']) && $pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($pagination['per_page']) && $pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($pagination['per_page']) && $pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ isset($pagination['per_page']) && $pagination['per_page'] == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                </div>
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

<!-- Modal Add Sub Categories -->
<div id="addSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="subCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD SUB CATEGORIES</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="createSubCategoryForm" action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Category Dropdown -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Categories</label>
                                <div class="relative">
                                    <select name="asset_type" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer" required>
                                        <option value="">Select Type</option>
                                        <option value="medical">Medical</option>
                                        <option value="non_medical">Non Medical</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub Category Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Sub Categories</label>
                                <input type="text" name="subcategory_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Sub Category -->
<div id="editSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editSubCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT SUB CATEGORY</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="editSubCategoryForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Hidden subcategory ID -->
                            <input type="hidden" id="editSubCategoryId" name="subcategory_id">

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Category</label>
                                <div class="relative">
                                    <select id="editAssetType" name="asset_type" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer" required>
                                        <option value="">Select Type</option>
                                        <option value="medical">Medical</option>
                                        <option value="non_medical">Non Medical</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Sub Category</label>
                                <input type="text" id="editSubCategoryName" name="subcategory_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Sub Category -->
<div id="deleteSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteSubCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE SUB CATEGORY</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Are you sure you want to delete this sub category? This action cannot be undone.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Cancel
                            </button>
                            <form id="deleteSubCategoryForm" action="" method="POST" class="w-1/2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" id="deleteSubCategoryId" name="subcategory_id">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addSubCategoryBtn = document.getElementById('addSubCategoryBtn');
        const addSubCategoryModal = document.getElementById('addSubCategoryModal');
        const editSubCategoryModal = document.getElementById('editSubCategoryModal');
        const deleteSubCategoryModal = document.getElementById('deleteSubCategoryModal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Function to change page
        window.changePage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = url.toString();
        }

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

        // Add Sub Category Modal
        addSubCategoryBtn.addEventListener('click', () => {
            openModal(addSubCategoryModal, addSubCategoryModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Sub Category Modal
        document.querySelectorAll('.edit-subcategory-btn').forEach(button => {
            button.addEventListener('click', () => {
                const subcategoryId = button.getAttribute('data-subcategory-id');
                const assetType = button.getAttribute('data-asset-type');
                const subcategoryName = button.getAttribute('data-subcategory-name');
                const description = button.getAttribute('data-description');

                // Update form action with the correct route and log it
                const formAction = "{{ route('categories.update', '') }}/" + subcategoryId;
                document.getElementById('editSubCategoryForm').action = formAction;
                console.log('Edit form action set to:', formAction);

                // Set form values
                document.getElementById('editSubCategoryId').value = subcategoryId;
                document.getElementById('editAssetType').value = assetType;
                document.getElementById('editSubCategoryName').value = subcategoryName;

                openModal(editSubCategoryModal, editSubCategoryModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Sub Category Modal
        document.querySelectorAll('.delete-subcategory-btn').forEach(button => {
            button.addEventListener('click', () => {
                const subcategoryId = button.getAttribute('data-subcategory-id');

                // Update form action with the correct route and log it
                const formAction = "{{ route('categories.destroy', '') }}/" + subcategoryId;
                document.getElementById('deleteSubCategoryForm').action = formAction;
                console.log('Delete form action set to:', formAction);

                // Set subcategory ID in hidden input
                document.getElementById('deleteSubCategoryId').value = subcategoryId;

                openModal(deleteSubCategoryModal, deleteSubCategoryModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Close Modal Handlers
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
            });
        });

        // Close on outside click
        [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal].forEach(modal => {
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
                [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal].forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });

        // Function to show toast notifications
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
    });
</script>
@endpush
@endsection
