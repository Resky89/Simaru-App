@extends('Layout.app')

@section('title', 'Role Management')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Role Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ROLE</h1>

                        <!-- Button Add Role -->
                        <button id="addRoleBtn"
                            class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="text-base">Add Role</span>
                        </button>
                    </div>

                    <!-- Role Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Role ID</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Role</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles['data'] ?? [] as $role)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                            <input type="checkbox" class="checkbox checkbox-sm" />
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['role_id'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['role_name'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['description'] ?? '-' }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex justify-center gap-2">
                                                <button class="text-[#3D3D3D] hover:text-[#213268] edit-role-btn"
                                                    data-role-id="{{ $role['role_id'] }}"
                                                    data-role-name="{{ $role['role_name'] }}"
                                                    data-description="{{ $role['description'] ?? '' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button class="text-[#3D3D3D] hover:text-red-500 delete-role-btn"
                                                    data-role-id="{{ $role['role_id'] }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                <button class="text-[#3D3D3D] hover:text-[#213268]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No roles found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for ROLE section -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $roles['pagination']['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $roles['pagination']['current_page'] ?? 1;
                                    $lastPage = $roles['pagination']['last_page'] ?? 1;
                                @endphp

                                @for ($i = 1; $i <= $lastPage; $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                        class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                            <a href="{{ $roles['pagination']['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) >= ($roles['pagination']['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
                                @if(isset($roles['pagination']) && is_array($roles['pagination']))
                                                            @php
                                                                $currentPage = $roles['pagination']['current_page'] ?? 1;
                                                                $perPage = $roles['pagination']['per_page'] ?? 10;
                                                                $total = $roles['pagination']['total'] ?? count($roles['data'] ?? []);
                                                                $from = ($currentPage - 1) * $perPage + 1;
                                                                $to = min($currentPage * $perPage, $total);
                                                            @endphp
                                                            Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                                @else
                                    Showing 1 to {{ count($roles['data'] ?? []) }} of {{ count($roles['data'] ?? []) }} entries
                                @endif
                            </span>
                            <select id="rolePerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeRolePerPage(this.value)">
                                <option value="10" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Role Modal -->
        <div id="addRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST">
                                @csrf
                                <div class="space-y-5 mx-auto">
                                    <!-- Role Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Role Name</label>
                                        <input type="text" name="role_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Enter role name" required>
                                    </div>

                                    <!-- Description Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Description</label>
                                        <textarea name="description"
                                            class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                            placeholder="Enter role description"></textarea>
                                    </div>

                                    <!-- Permissions Header -->
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <h3 class="text-lg font-bold text-[#213268] mb-2">PERMISSIONS</h3>
                                            <p class="text-sm text-gray-600 mb-4">Decide each user's privileges and what
                                                they can and can't do within your account.</p>

                                            <div class="flex flex-wrap gap-6 mt-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="add-all-permission"
                                                        class="checkbox checkbox-primary" data-target="all">
                                                    <label for="add-all-permission"
                                                        class="font-semibold cursor-pointer select-none">All
                                                        Permission</label>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="add-set-as-admin"
                                                        class="checkbox checkbox-primary">
                                                    <label for="add-set-as-admin"
                                                        class="font-semibold cursor-pointer select-none">Set As
                                                        Admin</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permission Groups Container -->
                                    <div id="add-permissions-container" class="space-y-6 pt-3">
                                        <p class="text-center text-gray-500 py-4">Loading permissions...</p>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-6">
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

        <!-- Edit Role Modal -->
        <div id="editRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editRoleForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-5 mx-auto">
                                    <!-- Role Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Role Name</label>
                                        <input type="text" id="edit_role_name" name="role_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Enter role name" required>
                                    </div>

                                    <!-- Description Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Description</label>
                                        <textarea id="edit_description" name="description"
                                            class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                            placeholder="Enter role description"></textarea>
                                    </div>

                                    <!-- Permissions Header -->
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <h3 class="text-lg font-bold text-[#213268] mb-2">PERMISSIONS</h3>
                                            <p class="text-sm text-gray-600 mb-4">Decide each user's privileges and what
                                                they can and can't do within your account.</p>

                                            <div class="flex flex-wrap gap-6 mt-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="edit-all-permission"
                                                        class="checkbox checkbox-primary" data-target="all">
                                                    <label for="edit-all-permission"
                                                        class="font-semibold cursor-pointer select-none">All
                                                        Permission</label>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="edit-set-as-admin"
                                                        class="checkbox checkbox-primary">
                                                    <label for="edit-set-as-admin"
                                                        class="font-semibold cursor-pointer select-none">Set As
                                                        Admin</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permission Groups Container -->
                                    <div id="edit-permissions-container" class="space-y-6 pt-3">
                                        <p class="text-center text-gray-500 py-4">Loading permissions...</p>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-6">
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

        <!-- Delete Role Modal -->
        <div id="deleteRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteRoleForm" action="" method="POST">
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
                                            role? This action cannot be undone.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteRoleModal">
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
            // Function to change items per page for roles
            window.changeRolePerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('role_limit', limit);
                url.searchParams.set('role_page', 1); // Reset to first page when changing limit
                window.location.href = url.toString();
            }

            // Toast container
            const toastContainer = document.createElement('div');
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
            document.body.appendChild(toastContainer);

            // Get all modal elements
            const addRoleModal = document.getElementById('addRoleModal');
            const editRoleModal = document.getElementById('editRoleModal');
            const deleteRoleModal = document.getElementById('deleteRoleModal');
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
            }

            // Function to fetch permissions
            async function fetchPermissions() {
                try {
                    const response = await fetch('{{ route("roles.permissions") }}');
                    if (!response.ok) {
                        throw new Error('Failed to fetch permissions');
                    }
                    const data = await response.json();
                    return data.data || [];
                } catch (error) {
                    console.error('Error fetching permissions:', error);
                    return [];
                }
            }

            // Function to render permission checkboxes - improved version
            function renderPermissionCheckboxes(permissions, selectedIds = [], containerId = 'add-permissions-container') {
                const container = document.getElementById(containerId);
                if (!container) return;

                if (!permissions || permissions.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-4">No permissions available</p>';
                    return;
                }

                // Group permissions by their category (first part before colon)
                const groupedPermissions = {};
                permissions.forEach(permission => {
                    if (permission.permission_name === '*') {
                        // Skip the all permissions one as we handle it separately
                        return;
                    }

                    let group = 'Other';
                    if (permission.permission_name.includes(':')) {
                        group = permission.permission_name.split(':')[0];
                        // Capitalize first letter
                        group = group.charAt(0).toUpperCase() + group.slice(1);
                    }

                    if (!groupedPermissions[group]) {
                        groupedPermissions[group] = [];
                    }
                    groupedPermissions[group].push(permission);
                });

                // Generate HTML for each group
                let html = '';
                for (const [group, perms] of Object.entries(groupedPermissions)) {
                    html += `
                                                                    <div class="permission-group bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-4">
                                                                        <h4 class="text-[#213268] text-lg font-semibold mb-3 capitalize">${group}</h4>
                                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">`;

                    perms.forEach(permission => {
                        const isChecked = selectedIds.includes(permission.permission_id);
                        const permId = `${containerId}-perm-${permission.permission_id}`;

                        // Map common permission types to more user-friendly names
                        let displayName = permission.permission_name;
                        if (permission.permission_name.includes(':')) {
                            const action = permission.permission_name.split(':')[1];

                            // Map common actions to more user-friendly names
                            switch (action) {
                                case 'view': displayName = 'View Only'; break;
                                case 'create': displayName = 'Add'; break;
                                case 'edit': displayName = 'Edit'; break;
                                case 'delete': displayName = 'Delete'; break;
                                case 'export': displayName = 'Export'; break;
                                default: displayName = action.charAt(0).toUpperCase() + action.slice(1);
                            }
                        }

                        html += `
                                                                        <div class="flex items-start gap-3 hover:bg-gray-50 p-2 rounded">
                                                                            <input type="checkbox"
                                                                                id="${permId}"
                                                                                name="permission_ids[]"
                                                                                value="${permission.permission_id}"
                                                                                class="checkbox checkbox-primary mt-1 permission-checkbox"
                                                                                data-group="${group.toLowerCase()}"
                                                                                ${isChecked ? 'checked' : ''}>
                                                                            <label for="${permId}" class="cursor-pointer select-none">
                                                                                <div class="font-medium">${displayName}</div>
                                                                                <div class="text-xs text-gray-500">${permission.description}</div>
                                                                            </label>
                                                                        </div>`;
                    });

                    html += `
                                                                        </div>
                                                                    </div>`;
                }

                container.innerHTML = html;

                // Find the "All" permission and set up the all permission checkbox
                const allPermission = permissions.find(p => p.permission_name === '*');
                if (allPermission) {
                    // Create a hidden input for the all permission
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'checkbox';
                    hiddenInput.name = 'permission_ids[]';
                    hiddenInput.value = allPermission.permission_id;
                    hiddenInput.id = `${containerId}-hidden-all-permission`;
                    hiddenInput.className = 'hidden';
                    hiddenInput.checked = selectedIds.includes(allPermission.permission_id);
                    container.appendChild(hiddenInput);

                    // Set the all permission checkbox state
                    const allCheckbox = document.querySelector(`#${containerId.replace('-permissions-container', '')}-all-permission`);
                    if (allCheckbox) {
                        allCheckbox.checked = selectedIds.includes(allPermission.permission_id);

                        // Toggle all checkboxes when the all permission is toggled
                        allCheckbox.addEventListener('change', function () {
                            const checkboxes = container.querySelectorAll('.permission-checkbox');
                            checkboxes.forEach(cb => {
                                cb.checked = this.checked;
                            });
                            document.getElementById(`${containerId}-hidden-all-permission`).checked = this.checked;
                        });
                    }
                }
            }

            // Update the edit role button event listener in Role.blade.php
            document.querySelectorAll('.edit-role-btn').forEach(button => {
                button.addEventListener('click', async () => {
                    const roleId = button.getAttribute('data-role-id');

                    try {
                        // Show loading state
                        document.getElementById('edit_role_name').value = "Loading...";
                        document.getElementById('edit_description').value = "Loading...";
                        document.getElementById('edit-permissions-container').innerHTML = '<p class="text-center text-gray-500 py-4">Loading role data...</p>';

                        // Open the modal while loading
                        openModal(editRoleModal, document.getElementById('editRoleModalContent'));

                        // Fetch role data from the API - use the named route
                        const response = await fetch(`{{ route('roles.show', '') }}/${roleId}`);

                        if (!response.ok) {
                            throw new Error('Failed to fetch role details');
                        }

                        // Parse the response
                        const roleData = await response.json();

                        if (!roleData.status) {
                            throw new Error(roleData.message || 'Failed to fetch role details');
                        }

                        // Set form action
                        document.getElementById('editRoleForm').action = `{{ route('roles.update', '') }}/${roleId}`;

                        // Populate form fields with data from API
                        const role = roleData.data;
                        document.getElementById('edit_role_name').value = role.role_name;
                        document.getElementById('edit_description').value = role.description || '';

                        // Load all permissions then select the ones from our role
                        const permissions = await fetchPermissions();
                        const selectedPermissionIds = role.permissions?.map(p => p.permission_id) || [];

                        // Render the permissions with the correct ones selected
                        renderPermissionCheckboxes(permissions, selectedPermissionIds, 'edit-permissions-container');

                    } catch (error) {
                        console.error('Error loading role:', error);
                        showToast(`Error loading role: ${error.message}`, 'error');

                        // Load permissions with empty selection in case of error
                        const permissions = await fetchPermissions();
                        renderPermissionCheckboxes(permissions, [], 'edit-permissions-container');
                    }
                });
            });

            // Add event listener for delete role button
            document.querySelectorAll('.delete-role-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const roleId = button.getAttribute('data-role-id');

                    // Set the form action for delete
                    document.getElementById('deleteRoleForm').action = `{{ route('roles.destroy', '') }}/${roleId}`;

                    // Open the delete modal
                    openModal(deleteRoleModal, document.getElementById('deleteRoleModalContent'));
                });
            });

            // Add event listener for add role button to open the modal
            document.getElementById('addRoleBtn').addEventListener('click', async () => {
                const permissions = await fetchPermissions();
                renderPermissionCheckboxes(permissions, [], 'add-permissions-container');

                // Reset the form
                document.getElementById('addRoleForm').reset();

                // Open the add modal
                openModal(addRoleModal, document.getElementById('addRoleModalContent'));
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
            [addRoleModal, editRoleModal, deleteRoleModal].forEach(modal => {
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
                    [addRoleModal, editRoleModal, deleteRoleModal].forEach(modal => {
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