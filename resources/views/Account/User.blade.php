@extends('Layout.app')

@section('title', 'User Management')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- User Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">USER</h1>

                        <!-- Button Add User -->
                        <button id="addUserBtn"
                            class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="text-base">Add User</span>
                        </button>
                    </div>

                    <!-- User Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">User ID</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Employee Number</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Roles</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users ?? [] as $user)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                            <input type="checkbox" class="checkbox checkbox-sm" />
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['user_id'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['employee_number'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($user['roles']) && is_array($user['roles']))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user['roles'] as $role)
                                                        <span class="px-2 py-1 bg-gray-100 rounded-full text-xs">
                                                            {{ $role['role_name'] ?? '-' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <span
                                                class="px-2 py-1 rounded text-xs {{ ($user['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ($user['is_active'] ?? false) ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex justify-center gap-2">
                                                <button class="text-[#3D3D3D] hover:text-[#213268] edit-user-btn"
                                                    data-user-id="{{ $user['user_id'] }}"
                                                    data-employee-number="{{ $user['employee_number'] }}"
                                                    data-role-ids="{{ isset($user['roles']) ? json_encode(array_column($user['roles'], 'role_id')) : '[]' }}"
                                                    data-is-active="{{ $user['is_active'] ? 'true' : 'false' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button class="text-[#3D3D3D] hover:text-red-500 delete-user-btn"
                                                    data-user-id="{{ $user['user_id'] }}">
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
                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No users found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for USERS section -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $user_pagination['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $user_pagination['current_page'] ?? 1;
                                    $lastPage = $user_pagination['last_page'] ?? 1;
                                @endphp

                                @for ($i = 1; $i <= $lastPage; $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['user_page' => $i]) }}"
                                        class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                            <a href="{{ $user_pagination['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) >= ($user_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">
                                @if(isset($user_pagination) && is_array($user_pagination))
                                                            @php
                                                                $currentPage = $user_pagination['current_page'] ?? 1;
                                                                $perPage = $user_pagination['per_page'] ?? 10;
                                                                $total = $user_pagination['total'] ?? count($users ?? []);
                                                                $from = ($currentPage - 1) * $perPage + 1;
                                                                $to = min($currentPage * $perPage, $total);
                                                            @endphp
                                                            Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                                @else
                                    Showing 1 to {{ count($users ?? []) }} of {{ count($users ?? []) }} entries
                                @endif
                            </span>
                            <select id="userPerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeUserPerPage(this.value)">
                                <option value="10" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All modals should be outside the main content section -->
        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD USER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="addUserForm" action="{{ route('users.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Employee Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Employee Number</label>
                                        <input type="text" name="employee_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Enter employee number" required>
                                    </div>

                                    <!-- Password Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Password</label>
                                        <input type="password" name="password"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Enter password" required>
                                    </div>

                                    <!-- Roles Selection for Add User Modal -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Roles</label>
                                        <div class="relative">
                                            <div
                                                class="w-full min-h-[45px] px-3 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus-within:border-[#213268] focus-within:ring-2 focus-within:ring-[#213268] focus-within:ring-opacity-20 transition-all duration-200">
                                                <div class="flex flex-wrap gap-2 mb-1">
                                                    <div id="add-selected-roles-display" class="flex flex-wrap gap-2"></div>
                                                    <div class="relative flex-grow min-w-[120px]">
                                                        <input type="text" id="add-roles-input"
                                                            class="w-full border-none focus:ring-0 p-0 py-1 text-sm bg-transparent"
                                                            placeholder="Type to search roles">
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                            <div id="add-roles-dropdown"
                                                class="absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg shadow-lg max-h-[200px] overflow-y-auto hidden">
                                                @foreach($roles['data'] as $role)
                                                    <div class="p-2 hover:bg-gray-50 role-option cursor-pointer"
                                                        data-role-id="{{ $role['role_id'] }}"
                                                        data-role-name="{{ $role['role_name'] }}" data-target="add">
                                                        <span class="text-sm text-gray-700">{{ $role['role_name'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div id="add-role-hidden-inputs"></div>
                                    </div>

                                    <!-- Active Status -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Status</label>
                                        <select name="is_active"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT USER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editUserForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Employee Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Employee Number</label>
                                        <input type="text" id="edit_employee_number" name="employee_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Enter employee number" required>
                                    </div>

                                    <!-- Roles Selection for Edit User Modal -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Roles</label>
                                        <div class="relative">
                                            <div
                                                class="w-full min-h-[45px] px-3 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus-within:border-[#213268] focus-within:ring-2 focus-within:ring-[#213268] focus-within:ring-opacity-20 transition-all duration-200">
                                                <div class="flex flex-wrap gap-2 mb-1">
                                                    <div id="edit-selected-roles-display" class="flex flex-wrap gap-2">
                                                    </div>
                                                    <div class="relative flex-grow min-w-[120px]">
                                                        <input type="text" id="edit-roles-input"
                                                            class="w-full border-none focus:ring-0 p-0 py-1 text-sm bg-transparent"
                                                            placeholder="Type to search roles">
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                            <div id="edit-roles-dropdown"
                                                class="absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg shadow-lg max-h-[200px] overflow-y-auto hidden">
                                                @foreach($roles['data'] as $role)
                                                    <div class="p-2 hover:bg-gray-50 role-option cursor-pointer"
                                                        data-role-id="{{ $role['role_id'] }}"
                                                        data-role-name="{{ $role['role_name'] }}" data-target="edit">
                                                        <span class="text-sm text-gray-700">{{ $role['role_name'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div id="edit-role-hidden-inputs"></div>
                                    </div>

                                    <!-- Active Status -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Status</label>
                                        <select id="edit_is_active" name="is_active"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div id="deleteUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE USER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteUserForm" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Are you sure you want to delete this
                                            user? This action cannot be undone.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteUserModal">
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to change items per page for users
            window.changeUserPerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('user_limit', limit);
                window.location.href = url.toString();
            }

            // Set up role search and selection for both modals
            setupRoleSearch('add-roles-input', 'add-roles-dropdown', 'add-selected-roles-display', 'add-role-hidden-inputs');
            setupRoleSearch('edit-roles-input', 'edit-roles-dropdown', 'edit-selected-roles-display', 'edit-role-hidden-inputs');

            // Setup role search functionality
            function setupRoleSearch(inputId, dropdownId, displayContainerId, hiddenInputsId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const displayContainer = document.getElementById(displayContainerId);
                const hiddenInputsContainer = document.getElementById(hiddenInputsId);
                const inputContainer = input.closest('.relative');

                // Store selected roles
                const selectedRoles = new Map();

                // Show dropdown when input is focused
                input.addEventListener('focus', function () {
                    if (this.value.trim() === '') {
                        // Show all available options
                        const options = dropdown.querySelectorAll('.role-option');
                        options.forEach(option => {
                            const roleId = option.getAttribute('data-role-id');
                            if (!selectedRoles.has(roleId)) {
                                option.classList.remove('hidden');
                            } else {
                                option.classList.add('hidden');
                            }
                        });

                        dropdown.classList.remove('hidden');
                    }
                });

                // Filter options while typing
                input.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim();

                    if (searchTerm.length > 0) {
                        // Show dropdown
                        dropdown.classList.remove('hidden');

                        // Filter options
                        const options = dropdown.querySelectorAll('.role-option');
                        let hasVisibleOptions = false;

                        options.forEach(option => {
                            const roleName = option.getAttribute('data-role-name').toLowerCase();
                            const roleId = option.getAttribute('data-role-id');

                            // Hide already selected roles and non-matching roles
                            if (selectedRoles.has(roleId) || !roleName.includes(searchTerm)) {
                                option.classList.add('hidden');
                            } else {
                                option.classList.remove('hidden');
                                hasVisibleOptions = true;
                            }
                        });

                        // Hide dropdown if no options match
                        if (!hasVisibleOptions) {
                            dropdown.classList.add('hidden');
                        }
                    } else {
                        // Show all available options when input is empty
                        const options = dropdown.querySelectorAll('.role-option');
                        let hasVisibleOptions = false;

                        options.forEach(option => {
                            const roleId = option.getAttribute('data-role-id');
                            if (!selectedRoles.has(roleId)) {
                                option.classList.remove('hidden');
                                hasVisibleOptions = true;
                            } else {
                                option.classList.add('hidden');
                            }
                        });

                        if (hasVisibleOptions) {
                            dropdown.classList.remove('hidden');
                        } else {
                            dropdown.classList.add('hidden');
                        }
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!inputContainer.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Handle role selection
                dropdown.addEventListener('click', function (e) {
                    const option = e.target.closest('.role-option');
                    if (option) {
                        const roleId = option.getAttribute('data-role-id');
                        const roleName = option.getAttribute('data-role-name');

                        // Add role if not already selected
                        if (!selectedRoles.has(roleId)) {
                            selectedRoles.set(roleId, roleName);
                            renderSelectedRoles();
                        }

                        // Clear input and hide dropdown
                        input.value = '';

                        // Show all unselected options
                        const options = dropdown.querySelectorAll('.role-option');
                        let hasVisibleOptions = false;

                        options.forEach(opt => {
                            const id = opt.getAttribute('data-role-id');
                            if (!selectedRoles.has(id)) {
                                opt.classList.remove('hidden');
                                hasVisibleOptions = true;
                            } else {
                                opt.classList.add('hidden');
                            }
                        });

                        if (hasVisibleOptions) {
                            dropdown.classList.remove('hidden');
                        } else {
                            dropdown.classList.add('hidden');
                        }

                        // Focus back on input for more selections
                        input.focus();
                    }
                });

                // Render selected roles
                function renderSelectedRoles() {
                    // Clear containers
                    displayContainer.innerHTML = '';
                    hiddenInputsContainer.innerHTML = '';

                    // Add badges and hidden inputs for each selected role
                    selectedRoles.forEach((roleName, roleId) => {
                        // Create badge
                        const badge = document.createElement('div');
                        badge.className = 'inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-md text-xs';
                        badge.innerHTML = `
                                <span>${roleName}</span>
                                <span class="cursor-pointer hover:text-red-500 font-medium" data-role-id="${roleId}">×</span>
                            `;

                        // Remove badge when clicking the x
                        badge.querySelector('span:last-child').addEventListener('click', function (e) {
                            e.stopPropagation();
                            const roleId = this.getAttribute('data-role-id');
                            selectedRoles.delete(roleId);
                            renderSelectedRoles();
                            input.focus();

                            // Update dropdown to show this option again
                            const option = dropdown.querySelector(`.role-option[data-role-id="${roleId}"]`);
                            if (option) {
                                option.classList.remove('hidden');
                            }
                        });

                        displayContainer.appendChild(badge);

                        // Create hidden input for form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'role_ids[]';
                        hiddenInput.value = roleId;
                        hiddenInputsContainer.appendChild(hiddenInput);
                    });
                }

                // Allow container click to focus the input
                inputContainer.addEventListener('click', function (e) {
                    if (e.target === this || e.target.closest('.flex.flex-wrap')) {
                        input.focus();
                    }
                });

                // Public method to set selected roles (for edit form)
                window[`set${inputId.split('-')[0].charAt(0).toUpperCase() + inputId.split('-')[0].slice(1)}SelectedRoles`] = function (roleIds, roleNames) {
                    // Clear existing selections
                    selectedRoles.clear();

                    // Add new selections
                    if (Array.isArray(roleIds) && Array.isArray(roleNames) && roleIds.length === roleNames.length) {
                        roleIds.forEach((id, index) => {
                            selectedRoles.set(id.toString(), roleNames[index]);
                        });
                    }

                    renderSelectedRoles();
                };
            }

            // Modified setupEditUserForm function to handle role names
            window.setupEditUserForm = function (userId, employeeNumber, roleIds, isActive) {
                const form = document.getElementById('editUserForm');
                form.action = `{{ route('users.update', '') }}/${userId}`;

                // Set employee number
                document.getElementById('edit_employee_number').value = employeeNumber;

                // Set is_active value
                document.getElementById('edit_is_active').value = (isActive === 'true') ? '1' : '0';

                // Get role names for the selected role IDs
                const roleNames = roleIds.map(roleId => {
                    const element = document.querySelector(`.role-option[data-role-id="${roleId}"]`);
                    return element ? element.getAttribute('data-role-name') : '';
                }).filter(name => name !== '');

                // Set selected roles
                window.setEditSelectedRoles(roleIds, roleNames);

                // Open the modal
                const modal = document.getElementById('editUserModal');
                const content = document.getElementById('editUserModalContent');
                openModal(modal, content);
            };

            // Reset role selection when opening Add User modal
            document.getElementById('addUserBtn').addEventListener('click', () => {
                window.setAddSelectedRoles([], []);
            });

            // Toast container
            const toastContainer = document.createElement('div');
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
            document.body.appendChild(toastContainer);

            // Get all modal elements
            const addUserModal = document.getElementById('addUserModal');
            const editUserModal = document.getElementById('editUserModal');
            const deleteUserModal = document.getElementById('deleteUserModal');
            const closeButtons = document.querySelectorAll('.close-modal');

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

                // Hide any open dropdowns
                document.getElementById('add-roles-dropdown').classList.add('hidden');
                document.getElementById('edit-roles-dropdown').classList.add('hidden');
            }

            // Edit User Modal
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-user-id');
                    const employeeNumber = this.getAttribute('data-employee-number');
                    const roleIds = JSON.parse(this.getAttribute('data-role-ids') || '[]');
                    const isActive = this.getAttribute('data-is-active');

                    console.log('Button data:', { userId, employeeNumber, roleIds, isActive }); // Debug

                    // Use the global function to set up the form
                    window.setupEditUserForm(userId, employeeNumber, roleIds, isActive);
                });
            });

            // Delete User Modal
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-user-id');
                    document.getElementById('deleteUserForm').action = `{{ route('users.destroy', '') }}/${userId}`;

                    openModal(deleteUserModal, deleteUserModal.querySelector('[id$="ModalContent"]'));
                });
            });

            // Close Modal Handlers
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                });
            });

            // Close on outside click
            [addUserModal, editUserModal, deleteUserModal].forEach(modal => {
                modal.addEventListener('click', function (e) {
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
                    [addUserModal, editUserModal, deleteUserModal].forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        }
                    });
                }
            });

            // Show toast notification
            window.showToast = function (message, type = 'info') {
                // Create toast element
                const toast = document.createElement('div');
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
                toast.innerHTML = `
                                                        <div class="flex items-center">
                                                            <div class="py-1">
                                                                ${icon}
                                                            </div>
                                                            <div>
                                                                <p class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}</p>
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
                    <p class="font-bold">Success!</p>
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
                    <p class="font-bold">Error!</p>
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
@endsection