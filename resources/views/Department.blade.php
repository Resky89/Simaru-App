@extends('Layout.app')

@section('title', 'Department')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Department Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DEPARTMENT</h1>

                    <!-- Button Add Department -->
                    <button id="addDepartmentBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Department</span>
                    </button>
                </div>

                <!-- Department Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">Department ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Department Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($departments) && count($departments) > 0)
                                @foreach($departments as $department)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $department['department_id'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $department['department_name'] }}</td>
                                    <td class="p-3 border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-department-btn"
                                                    data-id="{{ $department['department_id'] }}"
                                                    data-name="{{ $department['department_name'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-department-btn"
                                                    data-id="{{ $department['department_id'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-[#213268]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No departments found</td>
                                </tr>
                            @endif
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
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $lastPage = $pagination['last_page'] ?? $currentPage;
                                @endphp

                                @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                    <button onclick="changePage({{ $i }})"
                                            class="w-8 h-8 flex items-center justify-center {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268] hover:bg-gray-50' }} rounded text-sm">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <!-- Default static pagination if pagination data is not available -->
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
                                    $total = $pagination['total'] ?? count($departments);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($departments) }} of {{ count($departments) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['per_page']) && $pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($pagination['per_page']) && $pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($pagination['per_page']) && $pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
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
@endif

@endsection

<!-- Modal Add Department -->
<div id="addDepartmentModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="departmentModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD DEPARTMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('departments.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Department Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Department Name</label>
                                <input type="text" name="department_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Enter Department Name" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Department -->
<div id="editDepartmentModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editDepartmentModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT DEPARTMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="editDepartmentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Department ID Input (Hidden) -->
                            <input type="hidden" id="edit_department_id" name="department_id">

                            <!-- Department Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Department Name</label>
                                <input type="text" id="edit_department_name" name="department_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Enter Department Name" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Department -->
<div id="deleteDepartmentModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteDepartmentModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE DEPARTMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <form id="deleteDepartmentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_department_id" name="department_id">
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Are you sure you want to delete this department? This action cannot be undone.</p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addDepartmentBtn = document.getElementById('addDepartmentBtn');
        const addDepartmentModal = document.getElementById('addDepartmentModal');
        const editDepartmentModal = document.getElementById('editDepartmentModal');
        const deleteDepartmentModal = document.getElementById('deleteDepartmentModal');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editDepartmentForm = document.getElementById('editDepartmentForm');
        const deleteDepartmentForm = document.getElementById('deleteDepartmentForm');

        // Show toast notifications for flash messages
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

        // Add Department Modal
        addDepartmentBtn.addEventListener('click', () => {
            openModal(addDepartmentModal, addDepartmentModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Department Modal
        document.querySelectorAll('.edit-department-btn').forEach(button => {
            button.addEventListener('click', () => {
                const departmentId = button.getAttribute('data-id');
                const departmentName = button.getAttribute('data-name');

                document.getElementById('edit_department_id').value = departmentId;
                document.getElementById('edit_department_name').value = departmentName;

                // Set the form action URL
                editDepartmentForm.action = `{{ route('departments.update', '') }}/${departmentId}`;

                openModal(editDepartmentModal, editDepartmentModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Department Modal
        document.querySelectorAll('.delete-department-btn').forEach(button => {
            button.addEventListener('click', () => {
                const departmentId = button.getAttribute('data-id');

                document.getElementById('delete_department_id').value = departmentId;

                // Set the form action URL
                deleteDepartmentForm.action = `{{ route('departments.destroy', '') }}/${departmentId}`;

                openModal(deleteDepartmentModal, deleteDepartmentModal.querySelector('[id$="ModalContent"]'));
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
        [addDepartmentModal, editDepartmentModal, deleteDepartmentModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                [addDepartmentModal, editDepartmentModal, deleteDepartmentModal].forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Create notification element
            const toast = document.createElement('div');

            // Set classes based on type
            let bgColor, borderColor, textColor, icon;
            if (type === 'success') {
                bgColor = 'bg-green-100';
                borderColor = 'border-green-500';
                textColor = 'text-green-700';
                icon = `<svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else if (type === 'error') {
                bgColor = 'bg-red-100';
                borderColor = 'border-red-500';
                textColor = 'text-red-700';
                icon = `<svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else {
                bgColor = 'bg-blue-100';
                borderColor = 'border-blue-500';
                textColor = 'text-blue-700';
                icon = `<svg class="h-6 w-6 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            }

            toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300`;
            toast.setAttribute('role', 'alert');

            // Create toast content
            toast.innerHTML = `
                <div class="flex items-center">
                    <div class="py-1">
                        ${icon}
                    </div>
                    <div>
                        <p class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}!</p>
                        <p>${message}</p>
                    </div>
                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                </div>
            `;

            // Add to container
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);

            // Remove after 5 seconds
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                setTimeout(() => {
                    if (toast.parentNode === toastContainer) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
    });
</script>
@endpush
